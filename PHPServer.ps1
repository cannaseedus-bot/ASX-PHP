<#
.SYNOPSIS
    PHPServer.ps1 - A complete PHP runtime server with JSON-RPC, REST API, DNS cache, file cache, and MCP support
.DESCRIPTION
    This script implements a PHP server that can parse and execute PHP code using our grammar definitions.
    It includes built-in routing, caching, DNS resolution, and MCP (Model Context Protocol) support.
.PARAMETER Port
    The port to listen on (default: 8080)
.PARAMETER DocumentRoot
    The document root directory (default: ./public)
.PARAMETER CacheDir
    Cache directory for file cache (default: ./cache)
.PARAMETER LogDir
    Log directory (default: ./logs)
.PARAMETER EnableMCP
    Enable MCP (Model Context Protocol) support (default: $true)
.EXAMPLE
    .\PHPServer.ps1 -Port 8080 -DocumentRoot ./public
.EXAMPLE
    .\PHPServer.ps1 -Port 3000 -EnableMCP $true
.NOTES
    Author: PHP Runtime
    Version: 1.0.0
#>

[CmdletBinding()]
param(
    [ValidateRange(1, 65535)]
    [int]$Port = 8080,
    [string]$DocumentRoot = "./public",
    [string]$CacheDir = "./cache",
    [string]$LogDir = "./logs",
    [bool]$EnableMCP = $true,
    [bool]$EnableDNS = $true,
    [bool]$EnableFileCache = $true,
    [bool]$EnableJSONRPC = $true,
    [bool]$EnableREST = $true
)

#region Setup and Initialization

# Set error handling
$ErrorActionPreference = "Stop"
$PSDefaultParameterValues['*:ErrorAction'] = 'Stop'

# Create directories if they don't exist
foreach ($dir in @($DocumentRoot, $CacheDir, $LogDir)) {
    if (!(Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
        Write-Host "Created directory: $dir" -ForegroundColor Green
    }
}

# Set up logging
$LogFile = Join-Path $LogDir "server.log"
$ErrorLogFile = Join-Path $LogDir "error.log"

function Write-Log {
    param([string]$Message, [string]$Level = "INFO")
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $logEntry = "[$timestamp] [$Level] $Message"
    Add-Content -Path $LogFile -Value $logEntry
    if ($Level -eq "ERROR" -or $Level -eq "WARNING") {
        Add-Content -Path $ErrorLogFile -Value $logEntry
    }
    Write-Host $logEntry -ForegroundColor $(if ($Level -eq "ERROR") { "Red" } elseif ($Level -eq "WARNING") { "Yellow" } else { "White" })
}

Write-Log "PHP Server starting on port $Port"

#region PHP Grammar Implementation (EBNF/PEG/JSON Schema based)

<#
    PHP Grammar-based Tokenizer and Parser
    Based on our EBNF, PEG, and JSON Schema definitions
#>

class PHPToken {
    [string]$Type
    [string]$Value
    [int]$Line
    [int]$Column
    
    PHPToken([string]$type, [string]$value, [int]$line, [int]$column) {
        $this.Type = $type
        $this.Value = $value
        $this.Line = $line
        $this.Column = $column
    }
    
    [string]ToString() {
        return "Token($($this.Type), '$($this.Value)', $($this.Line):$($this.Column))"
    }
}

class PHPLexer {
    [string]$Source
    [int]$Position
    [int]$Line
    [int]$Column
    [array]$Tokens
    
    PHPLexer([string]$source) {
        $this.Source = $source
        $this.Position = 0
        $this.Line = 1
        $this.Column = 1
        $this.Tokens = @()
    }
    
    [array]Tokenize() {
        while ($this.Position -lt $this.Source.Length) {
            $char = $this.Source[$this.Position]
            
            # Skip whitespace
            if ($char -match '\s') {
                if ($char -eq "`n") {
                    $this.Line++
                    $this.Column = 1
                } else {
                    $this.Column++
                }
                $this.Position++
                continue
            }
            
            # Skip comments
            if ($char -eq '/' -and $this.Position + 1 -lt $this.Source.Length) {
                $nextChar = $this.Source[$this.Position + 1]
                if ($nextChar -eq '/') {
                    # Single line comment
                    $this.SkipSingleLineComment()
                    continue
                } elseif ($nextChar -eq '*') {
                    # Multi-line comment
                    $this.SkipMultiLineComment()
                    continue
                }
            }
            
            # Skip # comments
            if ($char -eq '#') {
                $this.SkipSingleLineComment()
                continue
            }
            
            # Handle strings
            if ($char -eq '"' -or $char -eq "'") {
                $this.Tokens += $this.ScanString($char)
                continue
            }
            
            # Handle heredoc/nowdoc
            if ($char -eq '<' -and $this.Position + 2 -lt $this.Source.Length -and $this.Source.Substring($this.Position, 3) -eq '<<<') {
                $this.Tokens += $this.ScanHeredoc()
                continue
            }
            
            # Handle identifiers
            if ($char -match '[a-zA-Z_\x7f-\xff]') {
                $this.Tokens += $this.ScanIdentifier()
                continue
            }
            
            # Handle numbers
            if ($char -match '\d') {
                $this.Tokens += $this.ScanNumber()
                continue
            }
            
            # Handle operators and special characters
            $this.Tokens += $this.ScanOperator()
        }
        
        # Add EOF token
        $this.Tokens += [PHPToken]::new('EOF', '', $this.Line, $this.Column)
        return $this.Tokens
    }
    
    [void]SkipSingleLineComment() {
        while ($this.Position -lt $this.Source.Length -and $this.Source[$this.Position] -ne "`n") {
            $this.Position++
        }
        $this.Position++  # Skip the newline
        $this.Line++
        $this.Column = 1
    }
    
    [void]SkipMultiLineComment() {
        $this.Position += 2  # Skip /*
        while ($this.Position -lt $this.Source.Length) {
            if ($this.Source[$this.Position] -eq '*' -and $this.Position + 1 -lt $this.Source.Length -and $this.Source[$this.Position + 1] -eq '/') {
                $this.Position += 2
                break
            }
            if ($this.Source[$this.Position] -eq "`n") {
                $this.Line++
            }
            $this.Position++
        }
    }
    
    [PHPToken]ScanString([char]$quote) {
        $startLine = $this.Line
        $startColumn = $this.Column
        $value = ""
        $this.Position++  # Skip opening quote
        
        while ($this.Position -lt $this.Source.Length) {
            $char = $this.Source[$this.Position]
            
            if ($char -eq $quote) {
                $this.Position++
                $this.Column++
                return [PHPToken]::new('STRING', $value, $startLine, $startColumn)
            }
            
            if ($char -eq '\') {
                # Escape character
                $this.Position++
                $this.Column++
                if ($this.Position -lt $this.Source.Length) {
                    $nextChar = $this.Source[$this.Position]
                    switch ($nextChar) {
                        'n' { $value += "`n" }
                        'r' { $value += "`r" }
                        't' { $value += "`t" }
                        '\' { $value += '\' }
                        '$' { $value += '$' }
                        '"' { $value += '"' }
                        "'" { $value += "'" }
                        default { $value += $nextChar }
                    }
                    $this.Position++
                    $this.Column++
                }
            } else {
                $value += $char
                $this.Position++
                $this.Column++
                if ($char -eq "`n") {
                    $this.Line++
                    $this.Column = 1
                }
            }
        }
        
        return [PHPToken]::new('STRING', $value, $startLine, $startColumn)
    }
    
    [PHPToken]ScanHeredoc() {
        $startLine = $this.Line
        $startColumn = $this.Column
        $this.Position += 3  # Skip <<<
        
        # Parse label
        $label = $this.ScanIdentifier().Value
        $this.Position++  # Skip newline after label
        $this.Line++
        $this.Column = 1
        
        $value = ""
        $endLabel = $label
        
        while ($this.Position -lt $this.Source.Length) {
            $currentLine = $this.ReadLine()
            if ($currentLine.Trim() -eq $endLabel) {
                break
            }
            $value += $currentLine + "`n"
        }
        
        return [PHPToken]::new('HEREDOC', $value.TrimEnd(), $startLine, $startColumn)
    }
    
    [string]ReadLine() {
        $currentLine = ""
        while ($this.Position -lt $this.Source.Length) {
            $char = $this.Source[$this.Position]
            if ($char -eq "`n") {
                $this.Position++
                $this.Line++
                $this.Column = 1
                break
            }
            $currentLine += $char
            $this.Position++
            $this.Column++
        }
        return $currentLine
    }
    
    [PHPToken]ScanIdentifier() {
        $startLine = $this.Line
        $startColumn = $this.Column
        $value = ""
        
        while ($this.Position -lt $this.Source.Length) {
            $char = $this.Source[$this.Position]
            if ($char -match '[a-zA-Z_\x7f-\xff0-9]') {
                $value += $char
                $this.Position++
                $this.Column++
            } else {
                break
            }
        }
        
        # Check for keywords
        $keywords = @('if', 'else', 'elseif', 'while', 'do', 'for', 'foreach', 'switch', 'case', 
                      'default', 'break', 'continue', 'return', 'throw', 'try', 'catch', 'finally',
                      'class', 'interface', 'trait', 'enum', 'function', 'use', 'namespace', 'declare',
                      'new', 'clone', 'yield', 'match', 'fn', 'abstract', 'final', 'public', 'protected',
                      'private', 'static', 'readonly', 'const', 'var', 'include', 'require', 'echo',
                      'print', 'die', 'exit', 'eval', 'isset', 'unset', 'empty', 'list', 'array',
                      'parent', 'self', 'static', 'string', 'int', 'float', 'bool', 'void', 'never',
                      'mixed', 'false', 'null', 'true', 'object', 'iterable', 'callable')
        
        if ($keywords -contains $value) {
            return [PHPToken]::new('KEYWORD', $value, $startLine, $startColumn)
        }
        
        return [PHPToken]::new('IDENTIFIER', $value, $startLine, $startColumn)
    }
    
    [PHPToken]ScanNumber() {
        $startLine = $this.Line
        $startColumn = $this.Column
        $value = ""
        $isFloat = $false
        
        # Handle hex
        if ($this.Position + 1 -lt $this.Source.Length -and $this.Source[$this.Position] -eq '0' -and $this.Source[$this.Position + 1] -match '[xX]') {
            $value += $this.Source[$this.Position]
            $this.Position++
            $this.Column++
            $value += $this.Source[$this.Position]
            $this.Position++
            $this.Column++
            while ($this.Position -lt $this.Source.Length -and $this.Source[$this.Position] -match '[0-9a-fA-F]') {
                $value += $this.Source[$this.Position]
                $this.Position++
                $this.Column++
            }
            return [PHPToken]::new('INTEGER', $value, $startLine, $startColumn)
        }
        
        # Handle binary
        if ($this.Position + 1 -lt $this.Source.Length -and $this.Source[$this.Position] -eq '0' -and $this.Source[$this.Position + 1] -match '[bB]') {
            $value += $this.Source[$this.Position]
            $this.Position++
            $this.Column++
            $value += $this.Source[$this.Position]
            $this.Position++
            $this.Column++
            while ($this.Position -lt $this.Source.Length -and $this.Source[$this.Position] -match '[01]') {
                $value += $this.Source[$this.Position]
                $this.Position++
                $this.Column++
            }
            return [PHPToken]::new('INTEGER', $value, $startLine, $startColumn)
        }
        
        # Parse number
        while ($this.Position -lt $this.Source.Length) {
            $char = $this.Source[$this.Position]
            if ($char -match '\d') {
                $value += $char
                $this.Position++
                $this.Column++
            } elseif ($char -eq '.' -and !$isFloat) {
                $value += $char
                $isFloat = $true
                $this.Position++
                $this.Column++
            } elseif (($char -eq 'e' -or $char -eq 'E') -and !$isFloat) {
                $value += $char
                $isFloat = $true
                $this.Position++
                $this.Column++
                if ($this.Position -lt $this.Source.Length -and $this.Source[$this.Position] -match '[+-]') {
                    $value += $this.Source[$this.Position]
                    $this.Position++
                    $this.Column++
                }
            } else {
                break
            }
        }
        
        if ($isFloat) {
            return [PHPToken]::new('FLOAT', $value, $startLine, $startColumn)
        }
        return [PHPToken]::new('INTEGER', $value, $startLine, $startColumn)
    }
    
    [PHPToken]ScanOperator() {
        $startLine = $this.Line
        $startColumn = $this.Column
        $char = $this.Source[$this.Position]
        
        # Multi-character operators
        $multiCharOperators = @('++', '--', '**', '<<', '>>', '<=>', '??', '&&', '||', '==', '===', '!=', '!==', '<=', '>=', '=>', '->', '?->')
        
        foreach ($op in $multiCharOperators) {
            if ($this.Position + $op.Length - 1 -lt $this.Source.Length) {
                $substring = $this.Source.Substring($this.Position, $op.Length)
                if ($substring -eq $op) {
                    $this.Position += $op.Length
                    $this.Column += $op.Length
                    return [PHPToken]::new('OPERATOR', $op, $startLine, $startColumn)
                }
            }
        }
        
        # Single character operators
        $singleCharOperators = @('+', '-', '*', '/', '%', '=', '!', '<', '>', '&', '|', '^', '~', '?', ':', ';', ',', '(', ')', '[', ']', '{', '}', '.', '@', '$')
        
        if ($singleCharOperators -contains $char) {
            $this.Position++
            $this.Column++
            return [PHPToken]::new('OPERATOR', $char, $startLine, $startColumn)
        }
        
        # Unknown character
        $this.Position++
        $this.Column++
        return [PHPToken]::new('UNKNOWN', $char, $startLine, $startColumn)
    }
}

class PHPParser {
    [array]$Tokens
    [int]$Position
    [hashtable]$AST
    
    PHPParser([array]$tokens) {
        $this.Tokens = $tokens
        $this.Position = 0
        $this.AST = @{}
    }
    
    [hashtable]Parse() {
        return $this.ParseProgram()
    }
    
    [hashtable]ParseProgram() {
        $program = @{
            type = "program"
            statements = @()
        }
        
        while ($this.Peek().Type -ne "EOF") {
            $stmt = $this.ParseStatement()
            if ($stmt) {
                $program.statements += $stmt
            }
        }
        
        return $program
    }
    
    [hashtable]ParseStatement() {
        $token = $this.Peek()
        
        switch ($token.Type) {
            "KEYWORD" {
                switch ($token.Value) {
                    "if" { return $this.ParseIfStatement() }
                    "switch" { return $this.ParseSwitchStatement() }
                    "while" { return $this.ParseWhileStatement() }
                    "do" { return $this.ParseDoStatement() }
                    "for" { return $this.ParseForStatement() }
                    "foreach" { return $this.ParseForeachStatement() }
                    "return" { return $this.ParseReturnStatement() }
                    "throw" { return $this.ParseThrowStatement() }
                    "try" { return $this.ParseTryStatement() }
                    "class" { return $this.ParseClassDeclaration() }
                    "interface" { return $this.ParseInterfaceDeclaration() }
                    "trait" { return $this.ParseTraitDeclaration() }
                    "enum" { return $this.ParseEnumDeclaration() }
                    "function" { return $this.ParseFunctionDeclaration() }
                    "namespace" { return $this.ParseNamespaceStatement() }
                    "use" { return $this.ParseUseStatement() }
                    "declare" { return $this.ParseDeclareStatement() }
                    "echo" { return $this.ParseEchoStatement() }
                    "new" { return $this.ParseNewExpression() }
                    default { return $this.ParseExpressionStatement() }
                }
            }
            "IDENTIFIER" {
                return $this.ParseExpressionStatement()
            }
            "OPERATOR" {
                if ($token.Value -eq ";") {
                    $this.Consume()
                    return @{ type = "empty" }
                }
                return $this.ParseExpressionStatement()
            }
        }
        return $this.ParseExpressionStatement()
    }
    
    [hashtable]ParseIfStatement() {
        $this.Consume() # if
        $this.Expect("OPERATOR", "(")
        $condition = $this.ParseExpression()
        $this.Expect("OPERATOR", ")")
        $then = $this.ParseStatement()
        
        $ifStmt = @{
            type = "if"
            condition = $condition
            then = $then
        }
        
        if ($this.Peek().Type -eq "KEYWORD" -and $this.Peek().Value -eq "elseif") {
            $ifStmt.elseif = @()
            while ($this.Peek().Type -eq "KEYWORD" -and $this.Peek().Value -eq "elseif") {
                $this.Consume()
                $this.Expect("OPERATOR", "(")
                $cond = $this.ParseExpression()
                $this.Expect("OPERATOR", ")")
                $stmt = $this.ParseStatement()
                $ifStmt.elseif += @{ condition = $cond; statement = $stmt }
            }
        }
        
        if ($this.Peek().Type -eq "KEYWORD" -and $this.Peek().Value -eq "else") {
            $this.Consume()
            $ifStmt.else = $this.ParseStatement()
        }
        
        return $ifStmt
    }
    
    [hashtable]ParseSwitchStatement() {
        $this.Consume() # switch
        $this.Expect("OPERATOR", "(")
        $expression = $this.ParseExpression()
        $this.Expect("OPERATOR", ")")
        $this.Expect("OPERATOR", "{")
        
        $cases = @()
        while ($this.Peek().Type -ne "OPERATOR" -or $this.Peek().Value -ne "}") {
            if ($this.Peek().Type -eq "KEYWORD" -and $this.Peek().Value -eq "case") {
                $this.Consume()
                $value = $this.ParseExpression()
                $this.Expect("OPERATOR", ":")
                $statements = @()
                while ($this.Peek().Type -ne "KEYWORD" -or ($this.Peek().Value -ne "case" -and $this.Peek().Value -ne "default" -and $this.Peek().Value -ne "}")) {
                    $stmt = $this.ParseStatement()
                    if ($stmt) { $statements += $stmt }
                    if ($this.Peek().Type -eq "EOF") { break }
                }
                $cases += @{ type = "case"; value = $value; statements = $statements }
            } elseif ($this.Peek().Type -eq "KEYWORD" -and $this.Peek().Value -eq "default") {
                $this.Consume()
                $this.Expect("OPERATOR", ":")
                $statements = @()
                while ($this.Peek().Type -ne "OPERATOR" -or $this.Peek().Value -ne "}") {
                    $stmt = $this.ParseStatement()
                    if ($stmt) { $statements += $stmt }
                    if ($this.Peek().Type -eq "EOF") { break }
                }
                $cases += @{ type = "default"; statements = $statements }
            } else {
                $this.Consume()
                if ($this.Peek().Type -eq "EOF") { break }
            }
        }
        
        $this.Expect("OPERATOR", "}")
        return @{ type = "switch"; expression = $expression; cases = $cases }
    }
    
    [hashtable]ParseWhileStatement() {
        $this.Consume() # while
        $this.Expect("OPERATOR", "(")
        $condition = $this.ParseExpression()
        $this.Expect("OPERATOR", ")")
        $body = $this.ParseStatement()
        return @{ type = "while"; condition = $condition; body = $body }
    }
    
    [hashtable]ParseDoStatement() {
        $this.Consume() # do
        $body = $this.ParseStatement()
        $this.Expect("KEYWORD", "while")
        $this.Expect("OPERATOR", "(")
        $condition = $this.ParseExpression()
        $this.Expect("OPERATOR", ")")
        $this.Expect("OPERATOR", ";")
        return @{ type = "do_while"; condition = $condition; body = $body }
    }
    
    [hashtable]ParseForStatement() {
        $this.Consume() # for
        $this.Expect("OPERATOR", "(")
        
        # Init
        $init = $null
        if ($this.Peek().Value -ne ";") {
            $init = $this.ParseExpression()
        }
        $this.Expect("OPERATOR", ";")
        
        # Condition
        $condition = $null
        if ($this.Peek().Value -ne ";") {
            $condition = $this.ParseExpression()
        }
        $this.Expect("OPERATOR", ";")
        
        # Increment
        $increment = $null
        if ($this.Peek().Value -ne ")") {
            $increment = $this.ParseExpression()
        }
        $this.Expect("OPERATOR", ")")
        
        $body = $this.ParseStatement()
        return @{ type = "for"; init = $init; condition = $condition; increment = $increment; body = $body }
    }
    
    [hashtable]ParseForeachStatement() {
        $this.Consume() # foreach
        $this.Expect("OPERATOR", "(")
        $expression = $this.ParseExpression()
        $this.Expect("KEYWORD", "as")
        
        $byRef = $false
        if ($this.Peek().Value -eq "&") {
            $this.Consume()
            $byRef = $true
        }
        
        $value = $this.ParseVariable()
        $key = $null
        
        if ($this.Peek().Value -eq "=>") {
            $this.Consume()
            if ($this.Peek().Value -eq "&") {
                $this.Consume()
            }
            $key = $value
            $value = $this.ParseVariable()
        }
        
        $this.Expect("OPERATOR", ")")
        $body = $this.ParseStatement()
        
        return @{ type = "foreach"; expression = $expression; key = $key; value = $value; body = $body; by_ref = $byRef }
    }
    
    [hashtable]ParseReturnStatement() {
        $this.Consume() # return
        $expression = $null
        if ($this.Peek().Value -ne ";") {
            $expression = $this.ParseExpression()
        }
        $this.Expect("OPERATOR", ";")
        return @{ type = "return"; expression = $expression }
    }
    
    [hashtable]ParseThrowStatement() {
        $this.Consume() # throw
        $expression = $this.ParseExpression()
        $this.Expect("OPERATOR", ";")
        return @{ type = "throw"; expression = $expression }
    }
    
    [hashtable]ParseTryStatement() {
        $this.Consume() # try
        $body = $this.ParseStatement()
        
        $catches = @()
        while ($this.Peek().Type -eq "KEYWORD" -and $this.Peek().Value -eq "catch") {
            $this.Consume()
            $this.Expect("OPERATOR", "(")
            $type = $this.ParseType()
            $variable = $this.ParseVariable()
            $this.Expect("OPERATOR", ")")
            $catchBody = $this.ParseStatement()
            $catches += @{ type = $type; variable = $variable; body = $catchBody }
        }
        
        $finally = $null
        if ($this.Peek().Type -eq "KEYWORD" -and $this.Peek().Value -eq "finally") {
            $this.Consume()
            $finally = $this.ParseStatement()
        }
        
        return @{ type = "try"; body = $body; catches = $catches; finally = $finally }
    }
    
    [hashtable]ParseFunctionDeclaration() {
        $this.Consume() # function
        $byRef = $false
        if ($this.Peek().Value -eq "&") {
            $this.Consume()
            $byRef = $true
        }
        
        $name = $this.Expect("IDENTIFIER").Value
        $this.Expect("OPERATOR", "(")
        $params = $this.ParseParameterList()
        $this.Expect("OPERATOR", ")")
        
        $returnType = $null
        if ($this.Peek().Value -eq ":") {
            $this.Consume()
            $nullable = $false
            if ($this.Peek().Value -eq "?") {
                $this.Consume()
                $nullable = $true
            }
            $returnType = $this.ParseType()
            if ($nullable) {
                $returnType.nullable = $true
            }
        }
        
        $body = $this.ParseStatement()
        return @{ type = "function"; name = $name; params = $params; return_type = $returnType; body = $body; by_ref = $byRef }
    }
    
    [hashtable]ParseClassDeclaration() {
        $this.Consume() # class
        $abstract = $false
        $final = $false
        $readonly = $false
        
        # Check for class modifiers
        while ($true) {
            if ($this.Peek().Type -eq "KEYWORD") {
                switch ($this.Peek().Value) {
                    "abstract" { $abstract = $true; $this.Consume() }
                    "final" { $final = $true; $this.Consume() }
                    "readonly" { $readonly = $true; $this.Consume() }
                    default { break }
                }
            } else {
                break
            }
        }
        
        $name = $this.Expect("IDENTIFIER").Value
        $extends = $null
        if ($this.Peek().Type -eq "KEYWORD" -and $this.Peek().Value -eq "extends") {
            $this.Consume()
            $extends = $this.Expect("IDENTIFIER").Value
        }
        
        $implements = @()
        if ($this.Peek().Type -eq "KEYWORD" -and $this.Peek().Value -eq "implements") {
            $this.Consume()
            do {
                $implements += $this.Expect("IDENTIFIER").Value
            } while ($this.Peek().Value -eq ",")
        }
        
        $this.Expect("OPERATOR", "{")
        $body = $this.ParseClassBody()
        $this.Expect("OPERATOR", "}")
        
        return @{
            type = "class"
            name = $name
            extends = $extends
            implements = $implements
            body = $body
            abstract = $abstract
            final = $final
            readonly = $readonly
        }
    }
    
    [array]ParseClassBody() {
        $members = @()
        
        while ($this.Peek().Type -ne "OPERATOR" -or $this.Peek().Value -ne "}") {
            if ($this.Peek().Type -eq "EOF") { break }
            $token = $this.Peek()
            switch ($token.Type) {
                "KEYWORD" {
                    switch ($token.Value) {
                        "public" { $members += $this.ParsePropertyOrMethod() }
                        "protected" { $members += $this.ParsePropertyOrMethod() }
                        "private" { $members += $this.ParsePropertyOrMethod() }
                        "static" { $members += $this.ParsePropertyOrMethod() }
                        "readonly" { $members += $this.ParsePropertyOrMethod() }
                        "const" { $members += $this.ParseConstDeclaration() }
                        "use" { $members += $this.ParseTraitUse() }
                        "function" { $members += $this.ParseMethodDeclaration() }
                        default { $this.Consume() }
                    }
                }
                "IDENTIFIER" {
                    $members += $this.ParsePropertyOrMethod()
                }
                default {
                    $this.Consume()
                }
            }
        }
        
        return $members
    }
    
    [hashtable]ParsePropertyOrMethod() {
        $visibility = "public"
        $static = $false
        $readonly = $false
        
        while ($true) {
            $token = $this.Peek()
            if ($token.Type -eq "KEYWORD") {
                switch ($token.Value) {
                    "public" { $visibility = "public"; $this.Consume() }
                    "protected" { $visibility = "protected"; $this.Consume() }
                    "private" { $visibility = "private"; $this.Consume() }
                    "static" { $static = $true; $this.Consume() }
                    "readonly" { $readonly = $true; $this.Consume() }
                    default { break }
                }
            } else {
                break
            }
        }
        
        # Check if this is a method or property
        if ($this.Peek().Type -eq "KEYWORD" -and $this.Peek().Value -eq "function") {
            return $this.ParseMethodDeclaration($visibility, $static)
        } else {
            return $this.ParsePropertyDeclaration($visibility, $static, $readonly)
        }
    }
    
    [hashtable]ParseMethodDeclaration($visibility, $static) {
        $this.Consume() # function
        $byRef = $false
        if ($this.Peek().Value -eq "&") {
            $this.Consume()
            $byRef = $true
        }
        
        $name = $this.Expect("IDENTIFIER").Value
        $this.Expect("OPERATOR", "(")
        $params = $this.ParseParameterList()
        $this.Expect("OPERATOR", ")")
        
        $returnType = $null
        if ($this.Peek().Value -eq ":") {
            $this.Consume()
            $returnType = $this.ParseType()
        }
        
        $body = $this.ParseStatement()
        
        return @{
            type = "method"
            name = $name
            params = $params
            return_type = $returnType
            body = $body
            visibility = $visibility
            static = $static
            by_ref = $byRef
        }
    }
    
    [hashtable]ParsePropertyDeclaration($visibility, $static, $readonly) {
        $type = $null
        if ($this.Peek().Type -eq "IDENTIFIER" -and ($this.Peek().Value -match "^(int|float|string|bool|array|callable|iterable|void|never|mixed|object|parent|self|static)$")) {
            $type = $this.ParseType()
        }
        
        $name = $this.ParseVariable()
        $default = $null
        if ($this.Peek().Value -eq "=") {
            $this.Consume()
            $default = $this.ParseExpression()
        }
        
        $this.Expect("OPERATOR", ";")
        
        return @{
            node_type = "property"
            name = $name.name
            prop_type = $type
            default = $default
            visibility = $visibility
            static = $static
            readonly = $readonly
        }
    }
    
    [hashtable]ParseConstDeclaration() {
        $this.Consume() # const
        $visibility = "public"
        
        $name = $this.Expect("IDENTIFIER").Value
        $this.Expect("OPERATOR", "=")
        $value = $this.ParseExpression()
        $this.Expect("OPERATOR", ";")
        
        return @{ type = "const"; name = $name; value = $value; visibility = $visibility }
    }
    
    [hashtable]ParseTraitUse() {
        $this.Consume() # use
        $traits = @()
        do {
            $traits += $this.Expect("IDENTIFIER").Value
        } while ($this.Peek().Value -eq ",")
        
        $adaptations = @()
        if ($this.Peek().Value -eq "{") {
            $this.Consume()
            while ($this.Peek().Value -ne "}") {
                # Parse trait adaptation
                $adaptation = @{}
                if ($this.Peek().Type -eq "IDENTIFIER") {
                    $trait = $this.Expect("IDENTIFIER").Value
                    $this.Expect("OPERATOR", "::")
                    $method = $this.Expect("IDENTIFIER").Value
                    
                    if ($this.Peek().Value -eq "insteadof") {
                        $this.Consume()
                        $target = $this.Expect("IDENTIFIER").Value
                        $this.Expect("OPERATOR", "::")
                        $targetMethod = $this.Expect("IDENTIFIER").Value
                        $adaptation = @{ type = "insteadof"; trait = $trait; method = $method; target = $target; target_method = $targetMethod }
                    } elseif ($this.Peek().Value -eq "as") {
                        $this.Consume()
                        $visibility = $null
                        if ($this.Peek().Type -eq "KEYWORD" -and ($this.Peek().Value -match "^(public|protected|private)$")) {
                            $visibility = $this.Consume().Value
                        }
                        $alias = $this.Expect("IDENTIFIER").Value
                        $adaptation = @{ type = "as"; trait = $trait; method = $method; visibility = $visibility; alias = $alias }
                    }
                }
                $adaptations += $adaptation
            }
            $this.Consume() # }
        }
        
        $this.Expect("OPERATOR", ";")
        return @{ type = "trait_use"; traits = $traits; adaptations = $adaptations }
    }
    
    [hashtable]ParseInterfaceDeclaration() {
        $this.Consume() # interface
        $name = $this.Expect("IDENTIFIER").Value
        $extends = @()
        
        if ($this.Peek().Type -eq "KEYWORD" -and $this.Peek().Value -eq "extends") {
            $this.Consume()
            do {
                $extends += $this.Expect("IDENTIFIER").Value
            } while ($this.Peek().Value -eq ",")
        }
        
        $this.Expect("OPERATOR", "{")
        $body = $this.ParseClassBody()
        $this.Expect("OPERATOR", "}")
        
        return @{ type = "interface"; name = $name; extends = $extends; body = $body }
    }
    
    [hashtable]ParseTraitDeclaration() {
        $this.Consume() # trait
        $name = $this.Expect("IDENTIFIER").Value
        $this.Expect("OPERATOR", "{")
        $body = $this.ParseClassBody()
        $this.Expect("OPERATOR", "}")
        
        return @{ type = "trait"; name = $name; body = $body }
    }
    
    [hashtable]ParseEnumDeclaration() {
        $this.Consume() # enum
        $name = $this.Expect("IDENTIFIER").Value
        $backingType = $null
        
        if ($this.Peek().Value -eq ":") {
            $this.Consume()
            $backingType = $this.Expect("IDENTIFIER").Value
        }
        
        $implements = @()
        if ($this.Peek().Type -eq "KEYWORD" -and $this.Peek().Value -eq "implements") {
            $this.Consume()
            do {
                $implements += $this.Expect("IDENTIFIER").Value
            } while ($this.Peek().Value -eq ",")
        }
        
        $this.Expect("OPERATOR", "{")
        $cases = @()
        $members = @()
        
        while ($this.Peek().Value -ne "}") {
            if ($this.Peek().Type -eq "EOF") { break }
            if ($this.Peek().Type -eq "KEYWORD" -and $this.Peek().Value -eq "case") {
                $this.Consume()
                $caseName = $this.Expect("IDENTIFIER").Value
                $caseValue = $null
                if ($this.Peek().Value -eq "=") {
                    $this.Consume()
                    $caseValue = $this.ParseExpression()
                }
                $this.Expect("OPERATOR", ";")
                $cases += @{ name = $caseName; value = $caseValue }
            } else {
                $members += $this.ParsePropertyOrMethod()
            }
        }
        
        $this.Expect("OPERATOR", "}")
        return @{ type = "enum"; name = $name; backing_type = $backingType; implements = $implements; cases = $cases; body = $members }
    }
    
    [hashtable]ParseNamespaceStatement() {
        $this.Consume() # namespace
        $name = $null
        
        if ($this.Peek().Type -eq "IDENTIFIER") {
            $name = $this.Expect("IDENTIFIER").Value
            if ($this.Peek().Value -eq ";") {
                $this.Consume()
                return @{ type = "namespace"; name = $name }
            }
        }
        
        if ($this.Peek().Value -eq "{") {
            $this.Consume()
            $statements = @()
            while ($this.Peek().Value -ne "}") {
                if ($this.Peek().Type -eq "EOF") { break }
                $stmt = $this.ParseStatement()
                if ($stmt) { $statements += $stmt }
            }
            $this.Expect("OPERATOR", "}")
            return @{ type = "namespace"; name = $name; statements = $statements }
        }
        
        return $null
    }
    
    [hashtable]ParseUseStatement() {
        $this.Consume() # use
        $items = @()
        $isFunction = $false
        $isConst = $false
        
        if ($this.Peek().Value -eq "function") {
            $isFunction = $true
            $this.Consume()
        } elseif ($this.Peek().Value -eq "const") {
            $isConst = $true
            $this.Consume()
        }
        
        do {
            $name = $this.Expect("IDENTIFIER").Value
            $alias = $null
            if ($this.Peek().Value -eq "as") {
                $this.Consume()
                $alias = $this.Expect("IDENTIFIER").Value
            }
            $items += @{ name = $name; alias = $alias; type = if ($isFunction) { "function" } elseif ($isConst) { "const" } else { "class" } }
        } while ($this.Peek().Value -eq ",")
        
        $this.Expect("OPERATOR", ";")
        return @{ type = "use"; items = $items }
    }
    
    [hashtable]ParseDeclareStatement() {
        $this.Consume() # declare
        $this.Expect("OPERATOR", "(")
        $directives = @{}
        
        do {
            $name = $this.Expect("IDENTIFIER").Value
            $this.Expect("OPERATOR", "=")
            $value = $this.ParseExpression()
            $directives[$name] = $value
        } while ($this.Peek().Value -eq ",")
        
        $this.Expect("OPERATOR", ")")
        $statement = $this.ParseStatement()
        
        return @{ type = "declare"; directives = $directives; statement = $statement }
    }
    
    [hashtable]ParseEchoStatement() {
        $this.Consume() # echo
        $expressions = @()
        do {
            $expressions += $this.ParseExpression()
        } while ($this.Peek().Value -eq ",")
        $this.Expect("OPERATOR", ";")
        return @{ type = "echo"; expressions = $expressions }
    }
    
    [hashtable]ParseExpressionStatement() {
        $expression = $this.ParseExpression()
        $this.Expect("OPERATOR", ";")
        return @{ type = "expression_statement"; expression = $expression }
    }
    
    [hashtable]ParseNewExpression() {
        $this.Consume() # new
        $className = $this.Expect("IDENTIFIER").Value
        $args = @()
        if ($this.Peek().Value -eq "(") {
            $this.Consume()
            $args = $this.ParseArgumentList()
            $this.Expect("OPERATOR", ")")
        }
        return @{ type = "new"; class = $className; arguments = $args }
    }
    
    [array]ParseParameterList() {
        $params = @()
        if ($this.Peek().Value -eq ")") {
            return $params
        }
        
        do {
            $param = @{}
            
            # Visibility modifier
            if ($this.Peek().Type -eq "KEYWORD" -and ($this.Peek().Value -match "^(public|protected|private)$")) {
                $param.visibility = $this.Consume().Value
            }
            
            # Readonly modifier
            if ($this.Peek().Type -eq "KEYWORD" -and $this.Peek().Value -eq "readonly") {
                $param.readonly = $true
                $this.Consume()
            }
            
            # Type declaration
            if ($this.Peek().Type -eq "IDENTIFIER" -and ($this.Peek().Value -match "^(int|float|string|bool|array|callable|iterable|void|never|mixed|object|parent|self|static)$")) {
                $param.type = $this.ParseType()
            }
            
            # By reference
            if ($this.Peek().Value -eq "&") {
                $this.Consume()
                $param.by_ref = $true
            }
            
            # Variadic
            if ($this.Peek().Value -eq "...") {
                $this.Consume()
                $param.variadic = $true
            }
            
            # Variable name
            $param.name = $this.ParseVariable().name
            
            # Default value
            if ($this.Peek().Value -eq "=") {
                $this.Consume()
                $param.default = $this.ParseExpression()
            }
            
            $params += $param
        } while ($this.Peek().Value -eq ",")
        
        return $params
    }
    
    [array]ParseArgumentList() {
        $args = @()
        if ($this.Peek().Value -eq ")") {
            return $args
        }
        
        do {
            $arg = @{}
            if ($this.Peek().Value -eq "...") {
                $this.Consume()
                $arg.spread = $true
            }
            $arg.value = $this.ParseExpression()
            $args += $arg
        } while ($this.Peek().Value -eq ",")
        
        return $args
    }
    
    [hashtable]ParseExpression() {
        return $this.ParseAssignmentExpression()
    }
    
    [hashtable]ParseAssignmentExpression() {
        $expr = $this.ParseConditionalExpression()
        
        if ($this.Peek().Value -match "^(=|\\+=|-=|\\*=|/=|\\.=|%=|&=|\\|=|\\^=|<<=|>>=|\\*\\*=|\\?\\?=)$") {
            $operator = $this.Consume().Value
            $right = $this.ParseAssignmentExpression()
            return @{ type = "assignment"; operator = $operator; left = $expr; right = $right }
        }
        
        return $expr
    }
    
    [hashtable]ParseConditionalExpression() {
        $expr = $this.ParseLogicalOrExpression()
        
        if ($this.Peek().Value -eq "?") {
            $this.Consume()
            if ($this.Peek().Value -eq ":") {
                # Elvis operator
                $this.Consume()
                $else = $this.ParseExpression()
                return @{ type = "conditional"; condition = $expr; else = $else; elvis = $true }
            } else {
                $then = $this.ParseExpression()
                $this.Expect("OPERATOR", ":")
                $else = $this.ParseExpression()
                return @{ type = "conditional"; condition = $expr; then = $then; else = $else }
            }
        }
        
        return $expr
    }
    
    [hashtable]ParseLogicalOrExpression() {
        $expr = $this.ParseLogicalAndExpression()
        
        while ($this.Peek().Value -match "^(or|\\|\\|)$") {
            $operator = $this.Consume().Value
            $right = $this.ParseLogicalAndExpression()
            $expr = @{ type = "binary_op"; operator = $operator; left = $expr; right = $right }
        }
        
        return $expr
    }
    
    [hashtable]ParseLogicalAndExpression() {
        $expr = $this.ParseBitwiseOrExpression()
        
        while ($this.Peek().Value -match "^(and|&&)$") {
            $operator = $this.Consume().Value
            $right = $this.ParseBitwiseOrExpression()
            $expr = @{ type = "binary_op"; operator = $operator; left = $expr; right = $right }
        }
        
        return $expr
    }
    
    [hashtable]ParseBitwiseOrExpression() {
        $expr = $this.ParseBitwiseXorExpression()
        
        while ($this.Peek().Value -eq "|") {
            $operator = $this.Consume().Value
            $right = $this.ParseBitwiseXorExpression()
            $expr = @{ type = "binary_op"; operator = $operator; left = $expr; right = $right }
        }
        
        return $expr
    }
    
    [hashtable]ParseBitwiseXorExpression() {
        $expr = $this.ParseBitwiseAndExpression()
        
        while ($this.Peek().Value -eq "^") {
            $operator = $this.Consume().Value
            $right = $this.ParseBitwiseAndExpression()
            $expr = @{ type = "binary_op"; operator = $operator; left = $expr; right = $right }
        }
        
        return $expr
    }
    
    [hashtable]ParseBitwiseAndExpression() {
        $expr = $this.ParseEqualityExpression()
        
        while ($this.Peek().Value -eq "&") {
            $operator = $this.Consume().Value
            $right = $this.ParseEqualityExpression()
            $expr = @{ type = "binary_op"; operator = $operator; left = $expr; right = $right }
        }
        
        return $expr
    }
    
    [hashtable]ParseEqualityExpression() {
        $expr = $this.ParseComparativeExpression()
        
        while ($this.Peek().Value -match "^(==|!=|===|!==|<=|>=|<|>)$") {
            $operator = $this.Consume().Value
            $right = $this.ParseComparativeExpression()
            $expr = @{ type = "binary_op"; operator = $operator; left = $expr; right = $right }
        }
        
        return $expr
    }
    
    [hashtable]ParseComparativeExpression() {
        $expr = $this.ParseShiftExpression()
        
        while ($this.Peek().Value -match "^(<|>|<=|>=)$") {
            $operator = $this.Consume().Value
            $right = $this.ParseShiftExpression()
            $expr = @{ type = "binary_op"; operator = $operator; left = $expr; right = $right }
        }
        
        return $expr
    }
    
    [hashtable]ParseShiftExpression() {
        $expr = $this.ParseAdditiveExpression()
        
        while ($this.Peek().Value -match "^(<<|>>)$") {
            $operator = $this.Consume().Value
            $right = $this.ParseAdditiveExpression()
            $expr = @{ type = "binary_op"; operator = $operator; left = $expr; right = $right }
        }
        
        return $expr
    }
    
    [hashtable]ParseAdditiveExpression() {
        $expr = $this.ParseMultiplicativeExpression()
        
        while ($this.Peek().Value -match "^(\\+|\\-)\\.?$") {
            $operator = $this.Consume().Value
            $right = $this.ParseMultiplicativeExpression()
            $expr = @{ type = "binary_op"; operator = $operator; left = $expr; right = $right }
        }
        
        return $expr
    }
    
    [hashtable]ParseMultiplicativeExpression() {
        $expr = $this.ParseExponentiationExpression()
        
        while ($this.Peek().Value -match "^(\\*|/|%)$") {
            $operator = $this.Consume().Value
            $right = $this.ParseExponentiationExpression()
            $expr = @{ type = "binary_op"; operator = $operator; left = $expr; right = $right }
        }
        
        return $expr
    }
    
    [hashtable]ParseExponentiationExpression() {
        $expr = $this.ParseUnaryExpression()
        
        while ($this.Peek().Value -eq "**") {
            $operator = $this.Consume().Value
            $right = $this.ParseUnaryExpression()
            $expr = @{ type = "binary_op"; operator = $operator; left = $expr; right = $right }
        }
        
        return $expr
    }
    
    [hashtable]ParseUnaryExpression() {
        if ($this.Peek().Value -match "^(\\+|\\-|!|~|@)$") {
            $operator = $this.Consume().Value
            $expr = $this.ParseUnaryExpression()
            return @{ type = "unary_op"; operator = $operator; operand = $expr }
        }
        
        return $this.ParsePostfixExpression()
    }
    
    [hashtable]ParsePostfixExpression() {
        $expr = $this.ParsePrimaryExpression()
        
        while ($true) {
            if ($this.Peek().Value -eq "[") {
                $this.Consume()
                $index = $this.ParseExpression()
                $this.Expect("OPERATOR", "]")
                $expr = @{ type = "array_access"; array = $expr; index = $index }
            } elseif ($this.Peek().Value -eq "{") {
                $this.Consume()
                $index = $this.ParseExpression()
                $this.Expect("OPERATOR", "}")
                $expr = @{ type = "array_access"; array = $expr; index = $index }
            } elseif ($this.Peek().Value -eq "->") {
                $this.Consume()
                $property = $this.Expect("IDENTIFIER").Value
                $expr = @{ type = "property_access"; object = $expr; property = $property }
            } elseif ($this.Peek().Value -eq "?->") {
                $this.Consume()
                $property = $this.Expect("IDENTIFIER").Value
                $expr = @{ type = "nullsafe_property_access"; object = $expr; property = $property }
            } elseif ($this.Peek().Value -eq "::") {
                $this.Consume()
                $property = $this.Expect("IDENTIFIER").Value
                $expr = @{ type = "static_property_access"; class = $expr; property = $property }
            } elseif ($this.Peek().Value -eq "(") {
                $this.Consume()
                $args = $this.ParseArgumentList()
                $this.Expect("OPERATOR", ")")
                $expr = @{ type = "function_call"; function = $expr; arguments = $args }
            } elseif ($this.Peek().Value -eq "++") {
                $this.Consume()
                $expr = @{ type = "post_increment"; operand = $expr }
            } elseif ($this.Peek().Value -eq "--") {
                $this.Consume()
                $expr = @{ type = "post_decrement"; operand = $expr }
            } else {
                break
            }
        }
        
        return $expr
    }
    
    [hashtable]ParsePrimaryExpression() {
        $token = $this.Peek()
        
        switch ($token.Type) {
            "IDENTIFIER" {
                $value = $this.Consume().Value
                if ($value -eq "true" -or $value -eq "false") {
                    return @{ type = "literal"; value = [bool]::Parse($value); literal_type = "boolean" }
                } elseif ($value -eq "null") {
                    return @{ type = "literal"; value = $null; literal_type = "null" }
                }
                return @{ type = "variable"; name = $value }
            }
            "STRING" {
                $value = $this.Consume().Value
                return @{ type = "literal"; value = $value; literal_type = "string" }
            }
            "INTEGER" {
                $value = [int]::Parse($this.Consume().Value)
                return @{ type = "literal"; value = $value; literal_type = "integer" }
            }
            "FLOAT" {
                $value = [float]::Parse($this.Consume().Value)
                return @{ type = "literal"; value = $value; literal_type = "float" }
            }
            "HEREDOC" {
                $value = $this.Consume().Value
                return @{ type = "literal"; value = $value; literal_type = "string" }
            }
            "OPERATOR" {
                if ($token.Value -eq "(") {
                    $this.Consume()
                    $expr = $this.ParseExpression()
                    $this.Expect("OPERATOR", ")")
                    return $expr
                } elseif ($token.Value -eq '$') {
                    return $this.ParseVariable()
                } elseif ($token.Value -eq "[") {
                    return $this.ParseArrayCreation()
                }
            }
        }
        
        # Default: try to parse as variable
        return $this.ParseVariable()
    }
    
    [hashtable]ParseVariable() {
        if ($this.Peek().Value -eq '$') {
            $this.Consume()
            if ($this.Peek().Type -eq "IDENTIFIER") {
                $name = $this.Consume().Value
                return @{ type = "variable"; name = $name }
            } elseif ($this.Peek().Value -eq "{") {
                $this.Consume()
                $expr = $this.ParseExpression()
                $this.Expect("OPERATOR", "}")
                return @{ type = "variable_variable"; expression = $expr }
            }
        }
        
        # Try to parse as identifier
        if ($this.Peek().Type -eq "IDENTIFIER") {
            $name = $this.Consume().Value
            return @{ type = "variable"; name = $name }
        }
        
        return $null
    }
    
    [hashtable]ParseArrayCreation() {
        $this.Consume() # [
        $items = @()
        
        if ($this.Peek().Value -ne "]") {
            do {
                $item = @{}
                if ($this.Peek().Value -eq "...") {
                    $this.Consume()
                    $item.spread = $true
                }
                $item.key = $null
                $item.value = $this.ParseExpression()
                
                if ($this.Peek().Value -eq "=>") {
                    $this.Consume()
                    $item.key = $item.value
                    $item.value = $this.ParseExpression()
                }
                $items += $item
            } while ($this.Peek().Value -eq ",")
        }
        
        $this.Expect("OPERATOR", "]")
        return @{ type = "array_creation"; items = $items }
    }
    
    [hashtable]ParseType() {
        $token = $this.Peek()
        if ($token.Type -eq "IDENTIFIER") {
            $typeName = $this.Consume().Value
            return @{ type = $typeName; nullable = $false }
        }
        return $null
    }
    
    [PHPToken]Peek() {
        if ($this.Position -lt $this.Tokens.Count) {
            return $this.Tokens[$this.Position]
        }
        return [PHPToken]::new('EOF', '', 0, 0)
    }
    
    [PHPToken]Consume() {
        $token = $this.Peek()
        if ($token) {
            $this.Position++
            return $token
        }
        return $null
    }
    
    [PHPToken]Expect($type, $value = $null) {
        $token = $this.Peek()
        if ($token.Type -eq $type -and ($value -eq $null -or $token.Value -eq $value)) {
            return $this.Consume()
        }
        throw "Expected $type '$value' but got $($token.Type) '$($token.Value)' at line $($token.Line):$($token.Column)"
    }
}

#region Cache System

class FileCache {
    [string]$CacheDir
    [int]$DefaultTTL
    
    FileCache([string]$cacheDir) {
        $this.CacheDir = $cacheDir
        $this.DefaultTTL = 3600
        if (!(Test-Path $cacheDir)) {
            New-Item -ItemType Directory -Path $cacheDir -Force | Out-Null
        }
    }
    
    FileCache([string]$cacheDir, [int]$defaultTTL) {
        $this.CacheDir = $cacheDir
        $this.DefaultTTL = $defaultTTL
        if (!(Test-Path $cacheDir)) {
            New-Item -ItemType Directory -Path $cacheDir -Force | Out-Null
        }
    }
    
    [void]Set([string]$key, $data, [int]$ttl = $null) {
        if ($ttl -eq $null) { $ttl = $this.DefaultTTL }
        $filename = $this.GetFilename($key)
        $expires = (Get-Date).AddSeconds($ttl)
        $cacheData = @{
            expires = $expires
            data = $data
            created = Get-Date
        }
        $json = $cacheData | ConvertTo-Json -Depth 10
        $json | Out-File -FilePath $filename -Encoding UTF8
    }
    
    [object]Get([string]$key) {
        $filename = $this.GetFilename($key)
        if (!(Test-Path $filename)) {
            return $null
        }
        
        try {
            $json = Get-Content -Path $filename -Raw -Encoding UTF8
            $cacheData = $json | ConvertFrom-Json
            if ($cacheData.expires -lt (Get-Date)) {
                Remove-Item -Path $filename -Force
                return $null
            }
            return $cacheData.data
        } catch {
            return $null
        }
    }
    
    [bool]Has([string]$key) {
        return $this.Get($key) -ne $null
    }
    
    [void]Delete([string]$key) {
        $filename = $this.GetFilename($key)
        if (Test-Path $filename) {
            Remove-Item -Path $filename -Force
        }
    }
    
    [void]Clear() {
        Get-ChildItem -Path $this.CacheDir -Filter "*.cache" | Remove-Item -Force
    }
    
    [hashtable]Stats() {
        $files = Get-ChildItem -Path $this.CacheDir -Filter "*.cache"
        $totalSize = ($files | Measure-Object -Property Length -Sum).Sum
        return @{
            entries = $files.Count
            size = $totalSize
            size_human = $this.FormatBytes($totalSize)
            directory = $this.CacheDir
        }
    }
    
    [string]GetFilename([string]$key) {
        $hash = [System.BitConverter]::ToString([System.Security.Cryptography.MD5]::Create().ComputeHash([System.Text.Encoding]::UTF8.GetBytes($key))).Replace("-", "").ToLower()
        return Join-Path $this.CacheDir "$hash.cache"
    }
    
    [string]FormatBytes($bytes) {
        if ($bytes -eq 0) { return "0 B" }
        $k = 1024
        $sizes = @("B", "KB", "MB", "GB")
        $i = [Math]::Floor([Math]::Log($bytes) / [Math]::Log($k))
        return "$([Math]::Round($bytes / [Math]::Pow($k, $i), 2)) $($sizes[$i])"
    }
}

#region DNS Resolver

class DnsResolver {
    [FileCache]$Cache
    [array]$Nameservers
    
    DnsResolver([FileCache]$cache) {
        $this.Cache = $cache
        $this.Nameservers = @("8.8.8.8", "1.1.1.1")
    }
    
    [array]Resolve([string]$domain, [string]$type = "A") {
        if (![System.Uri]::CheckHostName($domain) -ne "Dns") {
            return @{ error = "Invalid domain name" }
        }
        
        $cacheKey = "dns_${domain}_${type}"
        $cached = $this.Cache.Get($cacheKey)
        if ($cached -ne $null) {
            return $cached
        }
        
        $results = $this.Lookup($domain, $type)
        if ($results -and $results.Count -gt 0) {
            $this.Cache.Set($cacheKey, $results, 300)
        }
        
        return $results
    }
    
    [array]Lookup([string]$domain, [string]$type) {
        try {
            $records = @()
            $dnsRecords = Resolve-DnsName -Name $domain -Type $type -ErrorAction SilentlyContinue
            
            if ($dnsRecords) {
                foreach ($record in $dnsRecords) {
                    $records += @{
                        host = $domain
                        type = $type
                        address = if ($record.IPAddress) { $record.IPAddress } else { $record.Name }
                        ttl = 300
                    }
                }
                return $records
            }
        } catch {
            Write-Log "DNS lookup failed for ${domain}: $($_.Exception.Message)" "WARNING"
        }
        
        return @()
    }
    
    [array]ResolveBulk($domains) {
        $results = @{}
        foreach ($domain in $domains) {
            $results[$domain] = $this.Resolve($domain)
        }
        return $results
    }
}

#region JSON-RPC Server

class JsonRpcServer {
    [hashtable]$Methods
    [hashtable]$Notifications
    
    JsonRpcServer() {
        $this.Methods = @{}
        $this.Notifications = @{}
        $this.RegisterDefaultMethods()
    }
    
    [void]RegisterMethod([string]$method, [scriptblock]$handler) {
        $this.Methods[$method] = $handler
    }
    
    [void]RegisterNotification([string]$method, [scriptblock]$handler) {
        $this.Notifications[$method] = $handler
    }
    
    [object]Handle($request) {
        # Parse request
        if ($request -is [string]) {
            $request = $request | ConvertFrom-Json
        }
        
        # Handle batch requests
        if ($request -is [array]) {
            $responses = @()
            foreach ($req in $request) {
                $resp = $this.HandleSingle($req)
                if ($resp) { $responses += $resp }
            }
            return $responses
        }
        
        return $this.HandleSingle($request)
    }
    
    [object]HandleSingle($request) {
        # Validate request
        $error = $this.ValidateRequest($request)
        if ($error) {
            return $error
        }
        
        $method = $request.method
        $params = $request.params
        $id = $request.id
        $isNotification = -not $request.PSObject.Properties.Name -contains "id"
        
        # Check if method exists
        if (-not $this.Methods.ContainsKey($method) -and -not $this.Notifications.ContainsKey($method)) {
            return @{
                jsonrpc = "2.0"
                error = @{ code = -32601; message = "Method not found" }
                id = $id
            }
        }
        
        try {
            $handler = if ($this.Methods.ContainsKey($method)) { $this.Methods[$method] } else { $this.Notifications[$method] }
            $result = & $handler $params
            
            if ($this.Methods.ContainsKey($method)) {
                return @{
                    jsonrpc = "2.0"
                    result = $result
                    id = $id
                }
            }
            
            return $null
        } catch {
            return @{
                jsonrpc = "2.0"
                error = @{ code = -32000; message = $_.Exception.Message }
                id = $id
            }
        }
    }
    
    [object]ValidateRequest($request) {
        if (-not $request -or -not $request.jsonrpc -or $request.jsonrpc -ne "2.0") {
            return @{
                jsonrpc = "2.0"
                error = @{ code = -32600; message = "Invalid Request" }
                id = $null
            }
        }
        
        if (-not $request.method -or -not [string]::IsNullOrEmpty($request.method)) {
            return @{
                jsonrpc = "2.0"
                error = @{ code = -32600; message = "Invalid Request" }
                id = $request.id
            }
        }
        
        return $null
    }
    
    [void]RegisterDefaultMethods() {
        $this.RegisterMethod("system.info", {
            return @{
                php_version = "8.2"
                server = "PHPServer.ps1"
                memory_limit = "128M"
                max_execution_time = 30
            }
        })
        
        $this.RegisterMethod("system.echo", { param($params)
            return $params.message ?? $params[0] ?? "No message"
        })
        
        $this.RegisterMethod("system.ping", {
            return @{ pong = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ss.fffZ") }
        })
    }
}

#region MCP (Model Context Protocol) Server

class McpServer {
    [hashtable]$Tools
    [hashtable]$Resources
    [hashtable]$Prompts
    [string]$Transport
    [hashtable]$ServerInfo
    [bool]$Initialized
    
    McpServer() {
        $this.Tools = @{}
        $this.Resources = @{}
        $this.Prompts = @{}
        $this.Transport = "http"
        $this.ServerInfo = @{
            name = "PHPServer.ps1"
            version = "1.0.0"
            protocol_version = "2025-03-26"
        }
        $this.Initialized = $false
        $this.RegisterDefaultTools()
    }
    
    [void]RegisterTool([string]$name, [string]$description, [scriptblock]$handler, $params = @{}) {
        $this.Tools[$name] = @{
            name = $name
            description = $description
            handler = $handler
            params = $params
            version = "1.0.0"
        }
    }
    
    [void]RegisterResource([string]$uri, [string]$name, [scriptblock]$handler, [string]$mimeType = "text/plain") {
        $this.Resources[$uri] = @{
            uri = $uri
            name = $name
            handler = $handler
            mimeType = $mimeType
        }
    }
    
    [void]RegisterPrompt([string]$name, [string]$description, [scriptblock]$handler, $params = @{}) {
        $this.Prompts[$name] = @{
            name = $name
            description = $description
            handler = $handler
            params = $params
        }
    }
    
    [object]HandleHttpRequest($request) {
        $message = $request
        if ($message -is [string]) {
            $message = $message | ConvertFrom-Json
        }
        
        if (-not $message -or -not $message.type) {
            return $this.ErrorResponse("Invalid MCP message")
        }
        
        return $this.ProcessMessage($message)
    }
    
    [object]ProcessMessage($message) {
        switch ($message.type) {
            "initialize" { return $this.HandleInitialize($message) }
            "tools/list" { return $this.HandleToolsList($message) }
            "tools/call" { return $this.HandleToolsCall($message) }
            "resources/list" { return $this.HandleResourcesList($message) }
            "resources/read" { return $this.HandleResourcesRead($message) }
            "prompts/list" { return $this.HandlePromptsList($message) }
            "prompts/get" { return $this.HandlePromptsGet($message) }
        }
        return $this.ErrorResponse("Unknown MCP message type: $($message.type)")
    }
    
    [object]HandleInitialize($message) {
        $this.Initialized = $true
        return @{
            type = "initialize_response"
            server_info = $this.ServerInfo
            capabilities = @{
                tools = $this.Tools.Count -gt 0
                resources = $this.Resources.Count -gt 0
                prompts = $this.Prompts.Count -gt 0
                streaming = $false
            }
        }
    }
    
    [object]HandleToolsList($message) {
        $toolList = @()
        foreach ($name in $this.Tools.Keys) {
            $tool = $this.Tools[$name]
            $toolList += @{
                name = $name
                description = $tool.description
                parameters = $tool.params
                version = $tool.version
            }
        }
        return @{ type = "tools/list_response"; tools = $toolList }
    }
    
    [object]HandleToolsCall($message) {
        $toolName = $message.name
        $params = $message.parameters
        
        if (-not $this.Tools.ContainsKey($toolName)) {
            return $this.ErrorResponse("Tool not found: $toolName")
        }
        
        try {
            $handler = $this.Tools[$toolName].handler
            $result = & $handler $params
            return @{ type = "tools/call_response"; result = $result; isError = $false }
        } catch {
            return @{ type = "tools/call_response"; result = @{ error = $_.Exception.Message }; isError = $true }
        }
    }
    
    [object]HandleResourcesList($message) {
        $resourceList = @()
        foreach ($uri in $this.Resources.Keys) {
            $resource = $this.Resources[$uri]
            $resourceList += @{
                uri = $uri
                name = $resource.name
                mimeType = $resource.mimeType
            }
        }
        return @{ type = "resources/list_response"; resources = $resourceList }
    }
    
    [object]HandleResourcesRead($message) {
        $uri = $message.uri
        if (-not $this.Resources.ContainsKey($uri)) {
            return $this.ErrorResponse("Resource not found: $uri")
        }
        
        try {
            $handler = $this.Resources[$uri].handler
            $result = & $handler
            return @{
                type = "resources/read_response"
                contents = @(
                    @{
                        uri = $uri
                        mimeType = $this.Resources[$uri].mimeType
                        text = $result
                    }
                )
            }
        } catch {
            return $this.ErrorResponse("Error reading resource: $($_.Exception.Message)")
        }
    }
    
    [object]HandlePromptsList($message) {
        $promptList = @()
        foreach ($name in $this.Prompts.Keys) {
            $prompt = $this.Prompts[$name]
            $promptList += @{
                name = $name
                description = $prompt.description
                parameters = $prompt.params
            }
        }
        return @{ type = "prompts/list_response"; prompts = $promptList }
    }
    
    [object]HandlePromptsGet($message) {
        $name = $message.name
        $params = $message.parameters
        
        if (-not $this.Prompts.ContainsKey($name)) {
            return $this.ErrorResponse("Prompt not found: $name")
        }
        
        try {
            $handler = $this.Prompts[$name].handler
            $result = & $handler $params
            return @{ type = "prompts/get_response"; prompt = $result }
        } catch {
            return $this.ErrorResponse("Error getting prompt: $($_.Exception.Message)")
        }
    }
    
    [object]ErrorResponse([string]$message, [int]$code = -1) {
        return @{
            type = "error"
            error = @{ code = $code; message = $message }
        }
    }
    
    [void]RegisterDefaultTools() {
        $this.RegisterTool("echo", "Echo back a message", {
            param($params)
            return @{ echo = $params.message ?? "No message provided" }
        }, @{ message = @{ type = "string"; description = "Message to echo" } })
        
        $this.RegisterTool("dns_lookup", "Look up DNS records", {
            param($params)
            $domain = $params.domain
            if (-not $domain) { throw "Domain required" }
            $dns = [DnsResolver]::new([FileCache]::new($CacheDir))
            return $dns.Resolve($domain, $params.type ?? "A")
        }, @{
            domain = @{ type = "string"; description = "Domain to look up" }
            type = @{ type = "string"; description = "Record type (A, AAAA, MX, etc.)" }
        })
        
        $this.RegisterTool("cache_stats", "Get cache statistics", {
            param($params)
            $cache = [FileCache]::new($CacheDir)
            return $cache.Stats()
        }, @{ })
        
        $this.RegisterTool("clear_cache", "Clear the cache", {
            param($params)
            $cache = [FileCache]::new($CacheDir)
            $cache.Clear()
            return @{ cleared = $true }
        }, @{ })
    }
}

#region HTTP Server

class HttpServer {
    [string]$DocumentRoot
    [int]$Port
    [hashtable]$Routes
    [array]$Middleware
    [FileCache]$Cache
    [JsonRpcServer]$JsonRpc
    [McpServer]$Mcp
    [DnsResolver]$Dns
    [PHPLexer]$Lexer
    [PHPParser]$Parser
    
    HttpServer([int]$port, [string]$documentRoot, [string]$cacheDir) {
        $this.Port = $port
        $this.DocumentRoot = $documentRoot
        $this.Routes = @{}
        $this.Middleware = @()
        $this.Cache = [FileCache]::new($cacheDir)
        $this.JsonRpc = [JsonRpcServer]::new()
        $this.Mcp = [McpServer]::new()
        $this.Dns = [DnsResolver]::new($this.Cache)
        $this.RegisterDefaultRoutes()
    }
    
    [void]RegisterRoute([string]$method, [string]$path, [scriptblock]$handler) {
        $key = "$method`:$path"
        $this.Routes[$key] = $handler
    }
    
    [void]AddMiddleware([scriptblock]$middleware) {
        $this.Middleware += $middleware
    }
    
    [object]Dispatch([string]$method, [string]$uri, $input) {
        $path = $this.NormalizePath(([System.Uri]::new("http://localhost$uri")).AbsolutePath)
        
        # Run middleware
        $context = @{ method = $method; path = $path; input = $input }
        foreach ($mw in $this.Middleware) {
            $result = & $mw $context
            if ($result -ne $null) {
                return $result
            }
        }
        
        # Check REST prefix
        if ($path.StartsWith("/api/v1")) {
            return $this.HandleRest($method, $path, $input)
        }
        
        # Check RPC prefix
        if ($path.StartsWith("/api/rpc")) {
            return $this.HandleRpc($input)
        }
        
        # Check MCP prefix
        if ($path.StartsWith("/mcp")) {
            return $this.HandleMcp($input)
        }
        
        # Try route matching
        $key = "$method`:$path"
        if ($this.Routes.ContainsKey($key)) {
            $handler = $this.Routes[$key]
            return & $handler $input
        }
        
        # Serve static file
        return $this.ServeStaticFile($path)
    }
    
    [object]HandleRest([string]$method, [string]$path, $input) {
        $key = "$method`:$path"
        if ($this.Routes.ContainsKey($key)) {
            $handler = $this.Routes[$key]
            return & $handler $input
        }
        return @{ error = "Resource not found"; method = $method; path = $path }
    }
    
    [object]HandleRpc($input) {
        return $this.JsonRpc.Handle($input)
    }
    
    [object]HandleMcp($input) {
        return $this.Mcp.HandleHttpRequest($input)
    }
    
    [object]ServeStaticFile([string]$path) {
        $filePath = Join-Path $this.DocumentRoot $path
        if (Test-Path $filePath -PathType Leaf) {
            $content = Get-Content -Path $filePath -Raw
            return $content
        }
        
        # Try index.php
        $indexPath = Join-Path $this.DocumentRoot "index.php"
        if (Test-Path $indexPath) {
            return $this.ExecutePhpFile($indexPath)
        }
        
        return @{ error = "Not Found"; path = $path }
    }
    
    [string]ExecutePhpFile([string]$path) {
        try {
            $content = Get-Content -Path $path -Raw -Encoding UTF8
            $this.Lexer = [PHPLexer]::new($content)
            $tokens = $this.Lexer.Tokenize()
            $this.Parser = [PHPParser]::new($tokens)
            $ast = $this.Parser.Parse()
            
            # Execute the AST (simple interpretation)
            return $this.ExecuteAST($ast)
        } catch {
            Write-Log "Error executing PHP file: $($_.Exception.Message)" "ERROR"
            return "<h1>PHP Error</h1><p>$($_.Exception.Message)</p>"
        }
    }
    
    [string]ExecuteAST($ast) {
        # Simple AST interpreter
        $output = ""
        if ($ast.type -eq "program") {
            foreach ($stmt in $ast.statements) {
                $output += $this.ExecuteStatement($stmt)
            }
        }
        return $output
    }
    
    [string]ExecuteStatement($stmt) {
        if (-not $stmt) { return "" }
        
        switch ($stmt.type) {
            "echo" {
                $output = ""
                foreach ($expr in $stmt.expressions) {
                    $output += $this.ExecuteExpression($expr)
                }
                return $output
            }
            "expression_statement" {
                return $this.ExecuteExpression($stmt.expression)
            }
            "return" {
                if ($stmt.expression) {
                    return $this.ExecuteExpression($stmt.expression)
                }
                return ""
            }
            "if" {
                $condition = $this.ExecuteExpression($stmt.condition)
                if ($condition) {
                    return $this.ExecuteStatement($stmt.then)
                } elseif ($stmt.elseif) {
                    foreach ($elseif in $stmt.elseif) {
                        $cond = $this.ExecuteExpression($elseif.condition)
                        if ($cond) {
                            return $this.ExecuteStatement($elseif.statement)
                        }
                    }
                }
                if ($stmt.else) {
                    return $this.ExecuteStatement($stmt.else)
                }
                return ""
            }
            "while" {
                $output = ""
                $maxIterations = 1000
                $i = 0
                while ($this.ExecuteExpression($stmt.condition) -and $i -lt $maxIterations) {
                    $output += $this.ExecuteStatement($stmt.body)
                    $i++
                }
                return $output
            }
            "for" {
                $output = ""
                $maxIterations = 1000
                $i = 0
                if ($stmt.init) { $this.ExecuteExpression($stmt.init) }
                while ($this.ExecuteExpression($stmt.condition) -and $i -lt $maxIterations) {
                    $output += $this.ExecuteStatement($stmt.body)
                    if ($stmt.increment) { $this.ExecuteExpression($stmt.increment) }
                    $i++
                }
                return $output
            }
        }
        return ""
    }
    
    [object]ExecuteExpression($expr) {
        if (-not $expr) { return $null }
        
        switch ($expr.type) {
            "literal" { return $expr.value }
            "variable" {
                # Simple variable handling
                return $null
            }
            "binary_op" {
                $left = $this.ExecuteExpression($expr.left)
                $right = $this.ExecuteExpression($expr.right)
                switch ($expr.operator) {
                    "+" { return $left + $right }
                    "-" { return $left - $right }
                    "*" { return $left * $right }
                    "/" { return $left / $right }
                    "." { return "$left$right" }
                    "==" { return $left -eq $right }
                    "!=" { return $left -ne $right }
                    "===" { return $left -eq $right }
                    "!==" { return $left -ne $right }
                    "<" { return $left -lt $right }
                    ">" { return $left -gt $right }
                    "<=" { return $left -le $right }
                    ">=" { return $left -ge $right }
                    "&&" { return $left -and $right }
                    "||" { return $left -or $right }
                    "and" { return $left -and $right }
                    "or" { return $left -or $right }
                }
                return $null
            }
            "unary_op" {
                $operand = $this.ExecuteExpression($expr.operand)
                switch ($expr.operator) {
                    "!" { return -not $operand }
                    "-" { return -$operand }
                    "+" { return +$operand }
                }
                return $operand
            }
        }
        return $null
    }
    
    [void]RegisterDefaultRoutes() {
        # Service info
        $this.RegisterRoute("GET", "/", {
            return @{
                service = "PHPServer.ps1"
                version = "1.0.0"
                endpoints = @(
                    @{ path = "/"; method = "GET"; description = "Service info" }
                    @{ path = "/health"; method = "GET"; description = "Health check" }
                    @{ path = "/api/v1/cache/stats"; method = "GET"; description = "Cache statistics" }
                    @{ path = "/api/v1/dns"; method = "GET"; description = "DNS lookup" }
                    @{ path = "/api/rpc"; method = "POST"; description = "JSON-RPC endpoint" }
                    @{ path = "/mcp"; method = "POST"; description = "MCP endpoint" }
                )
            }
        })
        
        # Health check
        $this.RegisterRoute("GET", "/health", {
            return @{
                status = "ok"
                timestamp = (Get-Date).ToString("yyyy-MM-ddTHH:mm:ss.fffZ")
                cache = [FileCache]::new($CacheDir).Stats()
            }
        })
        
        # Cache stats
        $this.RegisterRoute("GET", "/api/v1/cache/stats", {
            return [FileCache]::new($CacheDir).Stats()
        })
        
        # Clear cache
        $this.RegisterRoute("DELETE", "/api/v1/cache", {
            $cache = [FileCache]::new($CacheDir)
            $cache.Clear()
            return @{ cleared = $true }
        })
        
        # DNS lookup
        $this.RegisterRoute("GET", "/api/v1/dns", {
            param($input)
            $domain = [System.Web.HttpUtility]::ParseQueryString([System.Uri]::new("http://localhost$($input)").Query)["domain"]
            $type = [System.Web.HttpUtility]::ParseQueryString([System.Uri]::new("http://localhost$($input)").Query)["type"] ?? "A"
            if (-not $domain) {
                return @{ error = "Domain parameter required" }
            }
            $dns = [DnsResolver]::new([FileCache]::new($CacheDir))
            return $dns.Resolve($domain, $type)
        })
        
        # PHP info
        $this.RegisterRoute("GET", "/api/v1/phpinfo", {
            return @{
                php_version = "8.2.0"
                extensions = @("json", "mbstring", "openssl")
                ini = @{
                    memory_limit = "128M"
                    max_execution_time = 30
                    upload_max_filesize = "2M"
                }
            }
        })
    }
    
    [string]NormalizePath([string]$path) {
        $path = $path.TrimEnd('/')
        if ([string]::IsNullOrEmpty($path)) {
            $path = "/"
        }
        return $path
    }
}

#region Server Startup

Write-Log "Initializing PHP Server components..."

# Create server instance
$server = [HttpServer]::new($Port, $DocumentRoot, $CacheDir)

# Add middleware
$server.AddMiddleware({
    param($context)
    Write-Log "$($context.method) $($context.path)"
    return $null
})

# Register MCP tools
$mcp = $server.Mcp
$mcp.RegisterResource("info://server", "Server Info", {
    return @{
        name = "PHPServer.ps1"
        version = "1.0.0"
        php_version = "8.2.0"
        platform = $PSVersionTable.PSVersion.ToString()
        time = (Get-Date).ToString("yyyy-MM-ddTHH:mm:ss.fffZ")
    } | ConvertTo-Json
}, "application/json")

$mcp.RegisterResource("info://cache", "Cache Stats", {
    return [FileCache]::new($CacheDir).Stats() | ConvertTo-Json
}, "application/json")

$mcp.RegisterPrompt("greeting", "A greeting prompt", {
    param($params)
    $name = $params.name ?? "World"
    return "Hello, $name! Welcome to PHPServer.ps1."
}, @{ name = @{ type = "string"; description = "Name to greet" } })

$mcp.RegisterPrompt("system_status", "System status prompt", {
    param($params)
    $status = @{
        server = "PHPServer.ps1"
        cache = [FileCache]::new($CacheDir).Stats()
        memory = [System.GC]::GetTotalMemory($false)
    }
    return "System Status: $($status | ConvertTo-Json)"
}, @{ })

# Register JSON-RPC methods
$jsonRpc = $server.JsonRpc
$jsonRpc.RegisterMethod("cache.get", { param($params)
    $key = $params.key ?? $params[0]
    if (-not $key) { throw "Cache key required" }
    $cache = [FileCache]::new($CacheDir)
    return $cache.Get($key)
})

$jsonRpc.RegisterMethod("cache.set", { param($params)
    $key = $params.key ?? $params[0]
    $value = $params.value ?? $params[1]
    $ttl = $params.ttl ?? $params[2]
    if (-not $key -or $value -eq $null) { throw "Key and value required" }
    $cache = [FileCache]::new($CacheDir)
    $cache.Set($key, $value, $ttl)
    return $true
})

$jsonRpc.RegisterMethod("cache.clear", {
    $cache = [FileCache]::new($CacheDir)
    $cache.Clear()
    return $true
})

$jsonRpc.RegisterMethod("dns.resolve", { param($params)
    $domain = $params.domain ?? $params[0]
    $type = $params.type ?? "A"
    if (-not $domain) { throw "Domain required" }
    $dns = [DnsResolver]::new([FileCache]::new($CacheDir))
    return $dns.Resolve($domain, $type)
})

$jsonRpc.RegisterMethod("mcp.tools", {
    return @{ tools = $mcp.Tools.Keys }
})

$jsonRpc.RegisterMethod("server.info", {
    return @{
        server = "PHPServer.ps1"
        version = "1.0.0"
        port = $Port
        document_root = $DocumentRoot
        cache_dir = $CacheDir
        log_dir = $LogDir
        capabilities = @{
            rest = $true
            json_rpc = $true
            mcp = $true
            dns = $true
            file_cache = $true
            php_parser = $true
        }
    }
})

Write-Log "All components initialized successfully"

#region HTTP Listener

Write-Log "Starting HTTP server on http://localhost:$Port" -ForegroundColor Green
Write-Log "Press Ctrl+C to stop the server"

$listener = [System.Net.HttpListener]::new()
$listener.Prefixes.Add("http://localhost:$Port/")
$listener.Start()

# Create a cancellation token source for graceful shutdown
$cts = [System.Threading.CancellationTokenSource]::new()
$token = $cts.Token

# Set up Ctrl+C handler
[Console]::TreatControlCAsInput = $false
Register-EngineEvent -SourceIdentifier PowerShell.Exiting -Action {
    Write-Log "Shutting down server..." "WARNING"
    $cts.Cancel()
} | Out-Null

Write-Log "Server started successfully!" -ForegroundColor Green
Write-Log "Endpoints:" -ForegroundColor Cyan
Write-Log "  REST API: http://localhost:$Port/api/v1/*" -ForegroundColor White
Write-Log "  JSON-RPC: http://localhost:$Port/api/rpc" -ForegroundColor White
Write-Log "  MCP: http://localhost:$Port/mcp" -ForegroundColor White
Write-Log "  Health: http://localhost:$Port/health" -ForegroundColor White
Write-Log "  Info: http://localhost:$Port/" -ForegroundColor White

while ($listener.IsListening -and -not $token.IsCancellationRequested) {
    try {
        $context = $listener.GetContextAsync().GetAwaiter().GetResult()
        
        # Process request in background using Start-ThreadJob-compatible script closure
        $ctx = $context
        Start-Job -ScriptBlock {
            param($ctx2, $server2)
            try {
                $request = $ctx2.Request
                $response = $ctx2.Response
                
                # Get request data
                $method = $request.HttpMethod
                $uri = $request.RawUrl
                
                # Read request body
                $input = $null
                if ($request.HasEntityBody) {
                    $reader = [System.IO.StreamReader]::new($request.InputStream)
                    $body = $reader.ReadToEnd()
                    try {
                        $input = $body | ConvertFrom-Json
                    } catch {
                        $input = $body
                    }
                }
                
                # Dispatch request
                $result = $server2.Dispatch($method, $uri, $input)
                
                # Send response
                $response.StatusCode = 200
                $response.ContentType = "application/json"
                
                if ($result -is [string]) {
                    $output = $result
                } else {
                    $output = $result | ConvertTo-Json -Depth 10
                }
                
                $bytes = [System.Text.Encoding]::UTF8.GetBytes($output)
                $response.OutputStream.Write($bytes, 0, $bytes.Length)
                $response.OutputStream.Close()
            } catch {
                try {
                    $ctx2.Response.StatusCode = 500
                    $ctx2.Response.ContentType = "application/json"
                    $errorMsg = @{ error = "Internal Server Error"; detail = $_.Exception.Message } | ConvertTo-Json
                    $bytes = [System.Text.Encoding]::UTF8.GetBytes($errorMsg)
                    $ctx2.Response.OutputStream.Write($bytes, 0, $bytes.Length)
                    $ctx2.Response.OutputStream.Close()
                } catch {}
            }
        } -ArgumentList $ctx, $server | Out-Null
        
    } catch {
        if (-not $token.IsCancellationRequested) {
            Write-Log "Error accepting connection: $($_.Exception.Message)" "ERROR"
        }
    }
}

$listener.Stop()
$listener.Close()

Write-Log "Server stopped" "WARNING"
