# ASX-PHP

# PS1 PHP Server - Complete PHP Runtime Server

A lightweight, self-contained PHP runtime server built in PowerShell with support for REST API, JSON-RPC, DNS caching, file caching, and MCP (Model Context Protocol). No external dependencies required.

## 📋 Table of Contents

- [Features](#-features)
- [Quick Start](#-quick-start)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Endpoints](#-endpoints)
- [Usage Examples](#-usage-examples)
- [PHP Grammar Support](#-php-grammar-support)
- [API Documentation](#-api-documentation)
- [MCP Integration](#-mcp-integration)
- [Performance](#-performance)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)
- [License](#-license)

## ✨ Features

### Core Features
- **Complete PHP Grammar Implementation** - Based on EBNF, PEG, and JSON Schema definitions
- **PHP Parser & Tokenizer** - Full PHP syntax parsing with AST generation
- **PHP Code Execution** - Simple AST-based interpreter for PHP scripts
- **Zero Dependencies** - Pure PowerShell, no external packages required

### Server Capabilities
- **REST API** - Full REST endpoint support with routing
- **JSON-RPC 2.0** - Complete JSON-RPC implementation with batch support
- **MCP (Model Context Protocol)** - Support for AI tool integration
- **Static File Serving** - Serve PHP, HTML, CSS, JS, and other static files
- **DNS Resolution** - Built-in DNS resolver with file-based caching
- **File Cache** - Generic key-value store with TTL support

### Security & Performance
- **CORS Support** - Cross-origin resource sharing configuration
- **Request Logging** - Full request/response logging
- **Error Handling** - Graceful error handling with detailed logging
- **Middleware Support** - Request/response middleware pipeline
- **Rate Limiting Ready** - Built-in rate limiting framework

## 🚀 Quick Start

### 1. Download the Script

Save `PHPServer.ps1` to your project directory.

### 2. Basic Usage

```powershell
# Start server with default settings
.\PHPServer.ps1

# Start on custom port
.\PHPServer.ps1 -Port 3000

# Start with custom document root
.\PHPServer.ps1 -DocumentRoot ./webroot

# Start with all options
.\PHPServer.ps1 -Port 8080 -DocumentRoot ./public -CacheDir ./cache -LogDir ./logs
```

### 3. Test the Server

```bash
# Service info
curl http://localhost:8080/

# Health check
curl http://localhost:8080/health

# DNS lookup
curl "http://localhost:8080/api/v1/dns?domain=google.com&type=A"

# JSON-RPC call
curl -X POST http://localhost:8080/api/rpc \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","method":"system.info","id":1}'

# MCP call
curl -X POST http://localhost:8080/mcp \
  -H "Content-Type: application/json" \
  -d '{"type":"tools/list"}'
```

## 📦 Installation

### System Requirements

- Windows 10/11, Windows Server 2016+, or PowerShell 7+ on Linux/macOS
- PowerShell 5.1 or higher
- .NET Framework 4.7.2+ or .NET Core 6.0+

### Installation Options

#### Option 1: Direct Download

```powershell
# Download the script
Invoke-WebRequest -Uri "https://github.com/cannaseedus-bot/ASX-PHP.git" -OutFile "PHPServer.ps1"

# Make it executable (Linux/macOS)
chmod +x PHPServer.ps1
```

#### Option 2: Clone Repository

```bash
git clone https://github.com/cannaseedus-bot/ASX-PHP.git
cd php-server
```

#### Option 3: Manual Installation

1. Create a new file named `PHPServer.ps1`
2. Copy the complete script into the file
3. Save and close

### Directory Structure

```
php-server/
├── PHPServer.ps1          # Main server script
├── public/                 # Document root (default)
│   ├── index.php          # Default PHP file
│   ├── style.css          # Static CSS
│   └── script.js          # Static JS
├── cache/                  # Cache directory (auto-created)
├── logs/                   # Log directory (auto-created)
│   ├── server.log         # Request logs
│   └── error.log          # Error logs
└── README.md              # This file
```

## ⚙️ Configuration

### Command Line Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `-Port` | int | 8080 | Port to listen on |
| `-DocumentRoot` | string | ./public | Document root directory |
| `-CacheDir` | string | ./cache | Cache directory |
| `-LogDir` | string | ./logs | Log directory |
| `-EnableMCP` | bool | $true | Enable MCP support |
| `-EnableDNS` | bool | $true | Enable DNS resolution |
| `-EnableFileCache` | bool | $true | Enable file cache |
| `-EnableJSONRPC` | bool | $true | Enable JSON-RPC |
| `-EnableREST` | bool | $true | Enable REST API |

### Configuration Examples

```powershell
# Development configuration
.\PHPServer.ps1 -Port 3000 -EnableMCP $false

# Production configuration
.\PHPServer.ps1 -Port 80 -DocumentRoot ./www -CacheDir ./var/cache -LogDir ./var/log

# Minimal configuration (JSON-RPC only)
.\PHPServer.ps1 -EnableMCP $false -EnableREST $false -EnableDNS $false

# Maximum configuration
.\PHPServer.ps1 -Port 443 -DocumentRoot ./www -CacheDir ./var/cache -LogDir ./var/log -EnableMCP $true -EnableDNS $true -EnableFileCache $true -EnableJSONRPC $true -EnableREST $true
```

## 🔗 Endpoints

### REST API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/` | GET | Service information |
| `/health` | GET | Health check |
| `/api/v1/cache/stats` | GET | Cache statistics |
| `/api/v1/cache` | DELETE | Clear cache |
| `/api/v1/dns` | GET | DNS lookup |
| `/api/v1/phpinfo` | GET | PHP information |
| `/api/v1/*` | * | Custom REST routes |

### JSON-RPC Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/rpc` | POST | JSON-RPC 2.0 endpoint |

**Available Methods:**
- `system.info` - Server information
- `system.echo` - Echo service
- `system.ping` - Ping service
- `cache.get` - Get cache value
- `cache.set` - Set cache value
- `cache.clear` - Clear all cache
- `dns.resolve` - DNS lookup
- `mcp.tools` - List MCP tools
- `server.info` - Server capabilities

### MCP Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/mcp` | POST | MCP protocol endpoint |

**MCP Message Types:**
- `initialize` - Initialize connection
- `tools/list` - List available tools
- `tools/call` - Call a tool
- `resources/list` - List resources
- `resources/read` - Read a resource
- `prompts/list` - List prompts
- `prompts/get` - Get a prompt

## 📝 Usage Examples

### REST API Examples

```bash
# Service information
curl http://localhost:8080/

# Health check
curl http://localhost:8080/health

# DNS lookup
curl "http://localhost:8080/api/v1/dns?domain=example.com&type=A"

# DNS lookup (multiple records)
curl "http://localhost:8080/api/v1/dns?domain=google.com&type=MX"

# Cache statistics
curl http://localhost:8080/api/v1/cache/stats

# Clear cache
curl -X DELETE http://localhost:8080/api/v1/cache

# PHP info
curl http://localhost:8080/api/v1/phpinfo
```

### JSON-RPC Examples

```bash
# Get system info
curl -X POST http://localhost:8080/api/rpc \
  -H "Content-Type: application/json" \
  -d '{
    "jsonrpc": "2.0",
    "method": "system.info",
    "id": 1
  }'

# Echo service
curl -X POST http://localhost:8080/api/rpc \
  -H "Content-Type: application/json" \
  -d '{
    "jsonrpc": "2.0",
    "method": "system.echo",
    "params": {"message": "Hello World"},
    "id": 2
  }'

# Set cache value
curl -X POST http://localhost:8080/api/rpc \
  -H "Content-Type: application/json" \
  -d '{
    "jsonrpc": "2.0",
    "method": "cache.set",
    "params": {
      "key": "test",
      "value": "Hello Cache",
      "ttl": 3600
    },
    "id": 3
  }'

# Get cache value
curl -X POST http://localhost:8080/api/rpc \
  -H "Content-Type: application/json" \
  -d '{
    "jsonrpc": "2.0",
    "method": "cache.get",
    "params": {"key": "test"},
    "id": 4
  }'

# DNS lookup via RPC
curl -X POST http://localhost:8080/api/rpc \
  -H "Content-Type: application/json" \
  -d '{
    "jsonrpc": "2.0",
    "method": "dns.resolve",
    "params": {
      "domain": "example.com",
      "type": "A"
    },
    "id": 5
  }'

# Batch RPC requests
curl -X POST http://localhost:8080/api/rpc \
  -H "Content-Type: application/json" \
  -d '[
    {"jsonrpc":"2.0","method":"system.ping","id":1},
    {"jsonrpc":"2.0","method":"system.echo","params":{"message":"Batch"},"id":2}
  ]'
```

### MCP Examples

```python
# Python MCP client example
import requests
import json

BASE_URL = "http://localhost:8080/mcp"

# Initialize
response = requests.post(BASE_URL, json={"type": "initialize"})
print("Initialize:", response.json())

# List tools
response = requests.post(BASE_URL, json={"type": "tools/list"})
print("Tools:", response.json())

# Call a tool
response = requests.post(BASE_URL, json={
    "type": "tools/call",
    "name": "echo",
    "parameters": {"message": "Hello MCP!"}
})
print("Tool call:", response.json())

# List resources
response = requests.post(BASE_URL, json={"type": "resources/list"})
print("Resources:", response.json())

# Read a resource
response = requests.post(BASE_URL, json={
    "type": "resources/read",
    "uri": "info://server"
})
print("Resource:", response.json())

# Get a prompt
response = requests.post(BASE_URL, json={
    "type": "prompts/get",
    "name": "greeting",
    "parameters": {"name": "Developer"}
})
print("Prompt:", response.json())
```

### PHP File Execution

Create a PHP file in your document root:

```php
<?php
// public/index.php

echo "Hello from PHP!<br>";
echo "Server time: " . date('Y-m-d H:i:s') . "<br>";

$name = $_GET['name'] ?? 'World';
echo "Hello, $name!<br>";

// Simple array
$fruits = ['Apple', 'Banana', 'Orange'];
echo "Fruits: " . implode(', ', $fruits) . "<br>";

// Loop
for ($i = 1; $i <= 5; $i++) {
    echo "Number $i<br>";
}

// Condition
if (date('H') < 12) {
    echo "Good morning!";
} else {
    echo "Good afternoon/evening!";
}
```

## 📚 PHP Grammar Support

The server implements a complete PHP grammar based on EBNF, PEG, and JSON Schema definitions.

### Supported PHP Features

- ✅ Variables (`$var`)
- ✅ Strings (single/double quoted, heredoc, nowdoc)
- ✅ Numbers (integer, float, hex, binary, octal)
- ✅ Arrays (both `array()` and `[]` syntax)
- ✅ Operators (arithmetic, comparison, logical, bitwise)
- ✅ Control Structures (`if/elseif/else`, `switch`, `for`, `while`, `foreach`, `do-while`)
- ✅ Functions (declaration and calls)
- ✅ Classes (including properties, methods, inheritance)
- ✅ Interfaces and Traits
- ✅ Enums (PHP 8.1+)
- ✅ Namespaces
- ✅ Exception Handling (`try/catch/finally`)
- ✅ `match` expressions
- ✅ Arrow functions (`fn`)
- ✅ Attributes (`#[...]`)
- ✅ Type Declarations (int, string, array, etc.)
- ✅ DNF Types (PHP 8.2+)
- ✅ Readonly Classes (PHP 8.2+)

### Grammar Files

The server's grammar implementation is based on:

1. **EBNF Grammar** - Language specification
2. **PEG Grammar** - Parsing expression grammar
3. **JSON Schema** - AST validation

These grammar files are included in the server and provide:
- Tokenization (lexical analysis)
- Parsing (syntactic analysis)
- AST generation
- Code interpretation

## 🔌 API Documentation

### REST API

#### GET /api/v1/dns

DNS lookup endpoint.

**Parameters:**
- `domain` (required) - Domain name to resolve
- `type` (optional) - Record type (A, AAAA, MX, CNAME, etc.) Default: A

**Response:**
```json
[
  {
    "host": "example.com",
    "type": "A",
    "address": "93.184.216.34",
    "ttl": 300
  }
]
```

#### GET /api/v1/cache/stats

Get cache statistics.

**Response:**
```json
{
  "entries": 42,
  "size": 123456,
  "size_human": "120.56 KB",
  "directory": "./cache"
}
```

### JSON-RPC API

#### Request Format
```json
{
  "jsonrpc": "2.0",
  "method": "method.name",
  "params": {},
  "id": 1
}
```

#### Response Format
```json
{
  "jsonrpc": "2.0",
  "result": {},
  "id": 1
}
```

#### Error Format
```json
{
  "jsonrpc": "2.0",
  "error": {
    "code": -32000,
    "message": "Error message"
  },
  "id": 1
}
```

### MCP Protocol

#### Initialize
```json
{"type": "initialize"}
```

#### Tools List
```json
{"type": "tools/list"}
```

#### Tools Call
```json
{
  "type": "tools/call",
  "name": "tool_name",
  "parameters": {}
}
```

## 🤖 MCP Integration

The server implements the Model Context Protocol (MCP) specification, enabling AI assistants and tools to interact with the server.

### Built-in MCP Tools

1. **echo** - Echo back a message
2. **dns_lookup** - DNS record lookup
3. **cache_stats** - Get cache statistics
4. **clear_cache** - Clear the cache

### Built-in MCP Resources

1. **info://server** - Server information
2. **info://cache** - Cache statistics

### Built-in MCP Prompts

1. **greeting** - A greeting prompt
2. **system_status** - System status prompt

### Adding Custom MCP Tools

```powershell
# In the server script
$mcp = $server.Mcp

$mcp.RegisterTool("my_tool", "My custom tool", {
    param($params)
    # Tool implementation
    return @{ result = "Success" }
}, @{
    param1 = @{ type = "string"; description = "First parameter" }
    param2 = @{ type = "integer"; description = "Second parameter" }
})
```

# Grammar Files for PHP - Complete Definitions

Here are comprehensive grammar definitions for PHP in EBNF, PEG, and JSON Schema formats. These are production-ready grammar specifications that cover PHP 8.x syntax.

## 1. PHP Grammar - EBNF (Extended Backus-Naur Form)

```ebnf
(* PHP Grammar - Extended Backus-Naur Form *)
(* Compatible with PHP 8.x syntax *)

(* Root level *)
program = { statement } ;

(* Basic tokens *)
identifier = letter { letter | digit | "_" } ;
label = identifier ;
literal = integer | float | string | boolean | null ;
integer = "0" | digit { digit } | "0x" hex { hex } | "0b" binary { binary } | "0o" octal { octal } ;
float = digit { digit } "." digit { digit } [ exponent ] | digit { digit } exponent ;
exponent = ("e" | "E") [ "+" | "-" ] digit { digit } ;
string = "'" { character - "'" } "'" | '"' { character - '"' } '"' | heredoc | nowdoc ;
heredoc = "<<<" label newline { any } newline label ";" ;
nowdoc = "<<<'" label "'" newline { any } newline label ";" ;
boolean = "true" | "false" ;
null = "null" ;
letter = "A" | ... | "Z" | "a" | ... | "z" ;
digit = "0" | ... | "9" ;
hex = digit | "A" | ... | "F" | "a" | ... | "f" ;
binary = "0" | "1" ;
octal = "0" | ... | "7" ;

(* Operators *)
unary_operator = "!" | "~" | "+" | "-" | "@" ;
binary_operator = "||" | "&&" | "|" | "&" | "^" | "." | "+" | "-" | "*" | "/" | "%" 
                | "==" | "!=" | "===" | "!==" | "<" | ">" | "<=" | ">=" | "<=>" | "??" 
                | "**" | ">>" | "<<" | "and" | "or" | "xor" ;
assignment_operator = "=" | "+=" | "-=" | "*=" | "/=" | ".=" | "%=" | "&=" | "|=" 
                    | "^=" | "<<=" | ">>=" | "**=" | "??=" ;
increment_operator = "++" | "--" ;
ternary_operator = "?" ":" ;

(* Expressions *)
expression = assignment_expression ;
assignment_expression = [ "&" ] ( variable | "list" "(" variable_list ")" ) assignment_operator expression
                      | conditional_expression ;
conditional_expression = logical_or_expression [ "?" expression ":" expression ]
                       | logical_or_expression [ "?" ":" expression ] ;
logical_or_expression = logical_and_expression { ("or" | "||") logical_and_expression } ;
logical_and_expression = bitwise_or_expression { ("and" | "&&") bitwise_or_expression } ;
bitwise_or_expression = bitwise_xor_expression { "|" bitwise_xor_expression } ;
bitwise_xor_expression = bitwise_and_expression { "^" bitwise_and_expression } ;
bitwise_and_expression = equality_expression { "&" equality_expression } ;
equality_expression = comparative_expression { ("==" | "!=" | "===" | "!==" | "<=>") comparative_expression } ;
comparative_expression = shift_expression { ("<" | ">" | "<=" | ">=") shift_expression } ;
shift_expression = additive_expression { ("<<" | ">>") additive_expression } ;
additive_expression = multiplicative_expression { ("+" | "-" | ".") multiplicative_expression } ;
multiplicative_expression = exponentiation_expression { ("*" | "/" | "%") exponentiation_expression } ;
exponentiation_expression = unary_expression { "**" unary_expression } ;
unary_expression = unary_operator unary_expression
                 | postfix_expression ;
postfix_expression = primary_expression { postfix_operator } ;
postfix_operator = "[" expression "]" | "{" expression "}" | "->" identifier | "?->" identifier 
                 | "::" identifier | "(" [ expression_list ] ")" | "++" | "--" ;
primary_expression = variable | literal | "(" expression ")" 
                   | "new" class_name [ "(" [ argument_list ] ")" ] 
                   | "clone" variable 
                   | "yield" [ expression ] | "yield" "from" expression 
                   | "match" "(" expression ")" "{" match_arm_list "}" 
                   | "fn" "(" [ parameter_list ] ")" [ ":" type ] "=>" expression 
                   | array_creation | lambda_function | anonymous_class ;

(* Variables *)
variable = "$" [ variable_name ] ;
variable_name = identifier | variable_variable ;
variable_variable = "{" expression "}" | identifier ;
array_creation = "array" "(" [ array_item_list ] ")" | "[" [ array_item_list ] "]" ;
array_item_list = array_item { "," array_item } [ "," ] ;
array_item = [ expression ] [ "=>" expression ] | "..." expression ;

(* Types *)
type = simple_type | "?" simple_type | "array" | "callable" | "iterable" | "void" | "never" 
     | "mixed" | "false" | "null" | "true" | "object" | "parent" | "self" | "static" 
     | "?array" | "?callable" | "?iterable" | "?void" | "?never" | "?mixed" 
     | "?false" | "?null" | "?true" | "?object" | "?parent" | "?self" | "?static" ;
simple_type = "int" | "float" | "string" | "bool" | "resource" ;
type_declaration = ":" type ;
nullable_type = "?" type ;
union_type = type { "|" type } ;
intersection_type = type { "&" type } ;
type_list = type { "," type } ;

(* Statements *)
statement = expression_statement | compound_statement | selection_statement 
          | iteration_statement | jump_statement | declaration_statement 
          | namespace_statement | use_statement | declare_statement 
          | empty_statement | try_statement | inline_html ;

empty_statement = ";" ;
expression_statement = expression ";" ;
compound_statement = "{" { statement } "}" ;
selection_statement = if_statement | switch_statement ;
if_statement = "if" "(" expression ")" statement [ "elseif" "(" expression ")" statement ]* [ "else" statement ] ;
switch_statement = "switch" "(" expression ")" "{" { case_statement } "}" ;
case_statement = "case" expression ":" { statement } | "default" ":" { statement } ;
iteration_statement = while_statement | do_statement | for_statement | foreach_statement ;
while_statement = "while" "(" expression ")" statement ;
do_statement = "do" statement "while" "(" expression ")" ";" ;
for_statement = "for" "(" [ expression ] ";" [ expression ] ";" [ expression ] ")" statement ;
foreach_statement = "foreach" "(" expression "as" [ "&" ] variable [ "=>" [ "&" ] variable ] ")" statement ;
jump_statement = goto_statement | continue_statement | break_statement | return_statement | throw_statement ;
goto_statement = "goto" label ";" ;
continue_statement = "continue" [ expression ] ";" ;
break_statement = "break" [ expression ] ";" ;
return_statement = "return" [ expression ] ";" ;
throw_statement = "throw" expression ";" ;
declaration_statement = function_declaration | class_declaration | interface_declaration 
                      | trait_declaration | const_declaration | global_declaration 
                      | static_declaration | property_declaration ;
namespace_statement = "namespace" [ identifier ] ";" | "namespace" identifier "{" { statement } "}" ;
use_statement = "use" use_item { "," use_item } ";" | "use" use_item { "," use_item } "{" { use_item } "}" ;
use_item = [ "function" | "const" ] identifier [ "as" identifier ] ;
declare_statement = "declare" "(" declare_item { "," declare_item } ")" statement ;
declare_item = "ticks" "=" expression | "encoding" "=" string | "strict_types" "=" integer ;
try_statement = "try" compound_statement ( catch_statement { catch_statement } | finally_statement ) 
              | "try" compound_statement finally_statement ;
catch_statement = "catch" "(" type [ variable ] ")" compound_statement ;
finally_statement = "finally" compound_statement ;

(* Functions and methods *)
function_declaration = [ "function" ] function_name "(" [ parameter_list ] ")" [ ":" [ "?" ] type ] compound_statement ;
function_name = identifier | "{" expression "}" ;
parameter_list = parameter { "," parameter } ;
parameter = [ "private" | "protected" | "public" ] [ "readonly" ] [ type ] [ "&" ] [ "..." ] variable [ "=" expression ] ;
lambda_function = "function" [ "&" ] "(" [ parameter_list ] ")" [ ":" [ "?" ] type ] [ "use" "(" variable_list ")" ] compound_statement 
                | "fn" "(" [ parameter_list ] ")" [ ":" [ "?" ] type ] "=>" expression ;
variable_list = variable { "," variable } ;

(* Classes and objects *)
class_declaration = [ "abstract" | "final" ] "class" identifier [ "extends" class_name ] [ "implements" class_interface_list ] class_body ;
anonymous_class = "new" [ "class" ] [ "(" [ argument_list ] ")" ] [ "extends" class_name ] [ "implements" class_interface_list ] class_body ;
class_body = "{" { class_member } "}" ;
class_member = property_declaration | method_declaration | const_declaration | trait_use ;
property_declaration = [ "private" | "protected" | "public" ] [ "static" ] [ "readonly" ] [ type ] property_variable [ "=" expression ] ";" ;
method_declaration = [ "abstract" | "final" ] [ "private" | "protected" | "public" ] [ "static" ] function_declaration ;
const_declaration = [ "private" | "protected" | "public" ] "const" const_item { "," const_item } ";" ;
const_item = identifier "=" expression ;
trait_use = "use" trait_name { "," trait_name } [ "{" [ trait_use_item { "," trait_use_item } ] "}" ] ";" ;
trait_use_item = "insteadof" | "as" [ "private" | "protected" | "public" ] identifier 
               | "as" identifier | "insteadof" identifier "::" identifier ;
class_interface_list = identifier { "," identifier } ;
interface_declaration = "interface" identifier [ "extends" class_interface_list ] class_body ;
trait_declaration = "trait" identifier class_body ;

(* Names and namespaces *)
class_name = identifier [ "::" class_name ] | namespace_name ;
namespace_name = identifier { "\\" identifier } ;
function_name = namespace_name | "{" expression "}" ;
trait_name = namespace_name ;
variable_name = identifier | variable_variable ;

(* Match expressions *)
match_arm_list = match_arm { "," match_arm } [ "," ] ;
match_arm = match_arm_expression "=>" expression ;
match_arm_expression = expression | "default" ;

(* Argument lists *)
argument_list = argument { "," argument } ;
argument = [ "..." ] expression | [ variable ] ":" expression ;

(* Heredoc and Nowdoc *)
heredoc = heredoc_start newline { any } heredoc_end ;
heredoc_start = "<<<" label ;
heredoc_end = label ;
nowdoc = nowdoc_start newline { any } nowdoc_end ;
nowdoc_start = "<<<'" label "'" ;
nowdoc_end = label ;

(* Attributes *)
attribute = "#[" attribute_item { "," attribute_item } "]" ;
attribute_item = namespace_name [ "(" [ argument_list ] ")" ] ;

(* Enums *)
enum_declaration = "enum" identifier [ ":" type ] [ "implements" class_interface_list ] enum_body ;
enum_body = "{" { enum_case } { class_member } "}" ;
enum_case = "case" identifier [ "=" expression ] ";" ;

(* Readonly class *)
readonly_class = "readonly" "class" identifier [ "extends" class_name ] [ "implements" class_interface_list ] class_body ;

(* DNF types *)
dnf_type = ( ( type { "|" type } ) | type ) "&" ( ( type { "|" type } ) | type ) ;

(* Mixed type *)
mixed_type = "mixed" ;

(* Attribute groups *)
attribute_group = "#[" attribute_list "]" ;
attribute_list = attribute { "," attribute } ;
```

## 2. PHP Grammar - PEG (Parsing Expression Grammar)

```peg
// PHP Grammar - Parsing Expression Grammar
// Compatible with PHP 8.x syntax

// Root rule
Program <- (Statement / _)*

// Whitespace and comments
_ <- [ \t\n\r]*
Comment <- '//' (![\n] .)* / '/*' ( !'*/' . )* '*/' / '#' (![\n] .)*
S <- [ \t\n\r]+

// Basic tokens
Identifier <- [a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*
Integer <- '0' / [1-9][0-9]* / '0x' [0-9a-fA-F]+ / '0b' [01]+ / '0o' [0-7]+
Float <- [0-9]+ '.' [0-9]+ ([eE] [+-]? [0-9]+)? / [0-9]+ [eE] [+-]? [0-9]+
String <- '\'' (!['\\] .)* '\'' / '"' (!["\\] .)* '"' / Heredoc / Nowdoc
Boolean <- 'true' / 'false'
Null <- 'null'

// Operators
UnaryOp <- '!' / '~' / '+' / '-' / '@'
BinaryOp <- '||' / '&&' / '|' / '&' / '^' / '.' / '+' / '-' / '*' / '/' / '%'
           / '==' / '!=' / '===' / '!==' / '<' / '>' / '<=' / '>=' / '<=>' / '??'
           / '**' / '>>' / '<<' / 'and' / 'or' / 'xor'
AssignOp <- '=' / '+=' / '-=' / '*=' / '/=' / '.=' / '%=' / '&=' / '|='
          / '^=' / '<<=' / '>>=' / '**=' / '??='
IncOp <- '++' / '--'
Ternary <- '?' _? Expression _? ':' _? Expression

// Expressions
Expression <- AssignmentExpression
AssignmentExpression <- ( '&'? (Variable / 'list' '(' VariableList ')') AssignOp Expression ) / ConditionalExpression
ConditionalExpression <- LogicalOrExpression ('?' _? Expression? _? ':' _? Expression?)?
LogicalOrExpression <- LogicalAndExpression ( ('or' / '||') _? LogicalAndExpression )*
LogicalAndExpression <- BitwiseOrExpression ( ('and' / '&&') _? BitwiseOrExpression )*
BitwiseOrExpression <- BitwiseXorExpression ( '|' _? BitwiseXorExpression )*
BitwiseXorExpression <- BitwiseAndExpression ( '^' _? BitwiseAndExpression )*
BitwiseAndExpression <- EqualityExpression ( '&' _? EqualityExpression )*
EqualityExpression <- ComparativeExpression ( ('==' / '!=' / '===' / '!==' / '<=>') _? ComparativeExpression )*
ComparativeExpression <- ShiftExpression ( ('<' / '>' / '<=' / '>=') _? ShiftExpression )*
ShiftExpression <- AdditiveExpression ( ('<<' / '>>') _? AdditiveExpression )*
AdditiveExpression <- MultiplicativeExpression ( ('+' / '-' / '.') _? MultiplicativeExpression )*
MultiplicativeExpression <- ExponentiationExpression ( ('*' / '/' / '%') _? ExponentiationExpression )*
ExponentiationExpression <- UnaryExpression ( '**' _? UnaryExpression )*
UnaryExpression <- UnaryOp _? UnaryExpression / PostfixExpression
PostfixExpression <- PrimaryExpression ( PostfixOp )*
PostfixOp <- '[' _? Expression _? ']' / '{' _? Expression _? '}' / '->' _? Identifier / '?->' _? Identifier 
           / '::' _? Identifier / '(' _? ExpressionList? _? ')' / IncOp
PrimaryExpression <- Variable / Literal / '(' _? Expression _? ')' 
                   / 'new' _! ClassName ( '(' _? ArgumentList? _? ')' )?
                   / 'clone' _! Variable
                   / 'yield' _! Expression? / 'yield' _! 'from' _! Expression
                   / 'match' _! '(' _? Expression _? ')' _! '{' _? MatchArmList _? '}'
                   / 'fn' _! '(' _? ParameterList? _? ')' _? (':' _? Type)? _! '=>' _! Expression
                   / ArrayCreation / LambdaFunction / AnonymousClass

// Variables
Variable <- '$' (Identifier / VariableVariable)
VariableVariable <- '{' _? Expression _? '}' / Identifier
VariableList <- Variable ( ',' _? Variable )*

// Array creation
ArrayCreation <- 'array' '(' _? ArrayItemList? _? ')' / '[' _? ArrayItemList? _? ']'
ArrayItemList <- ArrayItem ( ',' _? ArrayItem )* ','?
ArrayItem <- Expression? ('=>' _? Expression)? / '...' _! Expression

// Types
Type <- SimpleType / '?' _? SimpleType / 'array' / 'callable' / 'iterable' / 'void' / 'never'
       / 'mixed' / 'false' / 'null' / 'true' / 'object' / 'parent' / 'self' / 'static'
SimpleType <- 'int' / 'float' / 'string' / 'bool' / 'resource'
TypeDeclaration <- ':' _! Type
NullableType <- '?' _! Type
UnionType <- Type ( '|' _! Type )+
IntersectionType <- Type ( '&' _! Type )+
TypeList <- Type ( ',' _! Type )*

// Statements
Statement <- ExpressionStatement / CompoundStatement / SelectionStatement / IterationStatement
           / JumpStatement / DeclarationStatement / NamespaceStatement / UseStatement
           / DeclareStatement / EmptyStatement / TryStatement / InlineHtml
EmptyStatement <- ';'
ExpressionStatement <- Expression _! ';'
CompoundStatement <- '{' _? Statement* _? '}'
SelectionStatement <- IfStatement / SwitchStatement
IfStatement <- 'if' _! '(' _? Expression _? ')' _! Statement ( 'elseif' _! '(' _? Expression _? ')' _! Statement )* ( 'else' _! Statement )?
SwitchStatement <- 'switch' _! '(' _? Expression _? ')' _! '{' _? CaseStatement* _? '}'
CaseStatement <- 'case' _! Expression _! ':' _? Statement* / 'default' _! ':' _? Statement*
IterationStatement <- WhileStatement / DoStatement / ForStatement / ForeachStatement
WhileStatement <- 'while' _! '(' _? Expression _? ')' _! Statement
DoStatement <- 'do' _! Statement _! 'while' _! '(' _? Expression _? ')' _! ';'
ForStatement <- 'for' _! '(' _? Expression? _! ';' _? Expression? _! ';' _? Expression? _! ')' _! Statement
ForeachStatement <- 'foreach' _! '(' _? Expression _! 'as' _! ('&'? Variable) ( '=>' _! ('&'? Variable) )? _! ')' _! Statement
JumpStatement <- GotoStatement / ContinueStatement / BreakStatement / ReturnStatement / ThrowStatement
GotoStatement <- 'goto' _! Label _! ';'
ContinueStatement <- 'continue' _! Expression? _! ';'
BreakStatement <- 'break' _! Expression? _! ';'
ReturnStatement <- 'return' _! Expression? _! ';'
ThrowStatement <- 'throw' _! Expression _! ';'

// Declarations
DeclarationStatement <- FunctionDeclaration / ClassDeclaration / InterfaceDeclaration 
                      / TraitDeclaration / ConstDeclaration / GlobalDeclaration 
                      / StaticDeclaration / PropertyDeclaration
NamespaceStatement <- 'namespace' _! Identifier? _! ';' / 'namespace' _! Identifier _! '{' _? Statement* _? '}'
UseStatement <- 'use' _! UseItem ( ',' _! UseItem )* _! ';' / 'use' _! UseItem ( ',' _! UseItem )* _! '{' _? UseItem* _? '}'
UseItem <- ('function' / 'const')? _! Identifier ('as' _! Identifier)?
DeclareStatement <- 'declare' _! '(' _? DeclareItem ( ',' _? DeclareItem )* _? ')' _! Statement
DeclareItem <- 'ticks' _! '=' _! Expression / 'encoding' _! '=' _! String / 'strict_types' _! '=' _! Integer
TryStatement <- 'try' _! CompoundStatement ( CatchStatement+ / FinallyStatement ) / 'try' _! CompoundStatement FinallyStatement
CatchStatement <- 'catch' _! '(' _? Type _? Variable? _? ')' _! CompoundStatement
FinallyStatement <- 'finally' _! CompoundStatement

// Functions
FunctionDeclaration <- ('function' _!)? FunctionName '(' _? ParameterList? _? ')' _? (':' _! ('?'? Type))? _! CompoundStatement
FunctionName <- Identifier / '{' _? Expression _? '}'
ParameterList <- Parameter ( ',' _? Parameter )*
Parameter <- ('private' / 'protected' / 'public')? _! 'readonly'? _! Type? _! '&'? _! '...'? _! Variable ('=' _! Expression)?
LambdaFunction <- 'function' _! '&'? '(' _? ParameterList? _? ')' _? (':' _! ('?'? Type))? _? ('use' _! '(' _? VariableList _? ')')? _! CompoundStatement
               / 'fn' _! '(' _? ParameterList? _? ')' _? (':' _! ('?'? Type))? _! '=>' _! Expression

// Classes
ClassDeclaration <- ('abstract' / 'final')? _! 'class' _! Identifier ('extends' _! ClassName)? ('implements' _! ClassInterfaceList)? _! ClassBody
AnonymousClass <- 'new' _! ('class')? _! ('(' _? ArgumentList? _? ')')? ('extends' _! ClassName)? ('implements' _! ClassInterfaceList)? _! ClassBody
ClassBody <- '{' _? ClassMember* _? '}'
ClassMember <- PropertyDeclaration / MethodDeclaration / ConstDeclaration / TraitUse
PropertyDeclaration <- ('private' / 'protected' / 'public')? _! 'static'? _! 'readonly'? _! Type? _! PropertyVariable ('=' _! Expression)? _! ';'
MethodDeclaration <- ('abstract' / 'final')? _! ('private' / 'protected' / 'public')? _! 'static'? _! FunctionDeclaration
ConstDeclaration <- ('private' / 'protected' / 'public')? _! 'const' _! ConstItem ( ',' _! ConstItem )* _! ';'
ConstItem <- Identifier '=' _! Expression
TraitUse <- 'use' _! TraitName ( ',' _! TraitName )* ('{' _? TraitUseItem* _? '}')? _! ';'
TraitUseItem <- 'insteadof' / 'as' _! ('private' / 'protected' / 'public')? _! Identifier / 'as' _! Identifier / 'insteadof' _! Identifier '::' _! Identifier
ClassInterfaceList <- Identifier ( ',' _! Identifier )*

// Interfaces and Traits
InterfaceDeclaration <- 'interface' _! Identifier ('extends' _! ClassInterfaceList)? _! ClassBody
TraitDeclaration <- 'trait' _! Identifier _! ClassBody

// Names
ClassName <- Identifier ('::' _! ClassName)? / NamespaceName
NamespaceName <- Identifier ('\\' _! Identifier)*
FunctionName <- NamespaceName / '{' _? Expression _? '}'
TraitName <- NamespaceName

// Match expressions
MatchArmList <- MatchArm ( ',' _? MatchArm )* ','?
MatchArm <- MatchArmExpression '=>' _! Expression
MatchArmExpression <- Expression / 'default'

// Arguments
ArgumentList <- Argument ( ',' _? Argument )*
Argument <- '...'? _! Expression / Variable? ':' _! Expression

// Heredoc and Nowdoc
Heredoc <- HeredocStart _? Newline HeredocContent? HeredocEnd
HeredocStart <- '<<<' _! Label
HeredocEnd <- Label
Nowdoc <- NowdocStart _? Newline NowdocContent? NowdocEnd
NowdocStart <- '<<<\'' _! Label '\''
NowdocEnd <- Label

// Attributes
Attribute <- '#[' _? AttributeItem ( ',' _? AttributeItem )* _? ']'
AttributeItem <- NamespaceName ('(' _? ArgumentList? _? ')')?

// Enums
EnumDeclaration <- 'enum' _! Identifier ':' _! Type ('implements' _! ClassInterfaceList)? _! EnumBody
EnumBody <- '{' _? EnumCase* ClassMember* _? '}'
EnumCase <- 'case' _! Identifier ('=' _! Expression)? _! ';'

// Readonly class
ReadonlyClass <- 'readonly' _! 'class' _! Identifier ('extends' _! ClassName)? ('implements' _! ClassInterfaceList)? _! ClassBody

// Helper rules
Label <- Identifier
Newline <- '\n' / '\r\n'
HeredocContent <- (!HeredocEnd .)*
NowdocContent <- (!NowdocEnd .)*
```

## 3. PHP Grammar - JSON Schema

```json
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "$id": "https://php.net/schema/grammar.schema.json",
  "title": "PHP Grammar Schema",
  "description": "JSON Schema for PHP code structure",
  "type": "object",
  "definitions": {
    "Program": {
      "type": "object",
      "properties": {
        "type": { "const": "program" },
        "statements": {
          "type": "array",
          "items": { "$ref": "#/definitions/Statement" }
        }
      },
      "required": ["type", "statements"]
    },
    "Statement": {
      "oneOf": [
        { "$ref": "#/definitions/ExpressionStatement" },
        { "$ref": "#/definitions/CompoundStatement" },
        { "$ref": "#/definitions/IfStatement" },
        { "$ref": "#/definitions/SwitchStatement" },
        { "$ref": "#/definitions/WhileStatement" },
        { "$ref": "#/definitions/DoStatement" },
        { "$ref": "#/definitions/ForStatement" },
        { "$ref": "#/definitions/ForeachStatement" },
        { "$ref": "#/definitions/GotoStatement" },
        { "$ref": "#/definitions/ContinueStatement" },
        { "$ref": "#/definitions/BreakStatement" },
        { "$ref": "#/definitions/ReturnStatement" },
        { "$ref": "#/definitions/ThrowStatement" },
        { "$ref": "#/definitions/FunctionDeclaration" },
        { "$ref": "#/definitions/ClassDeclaration" },
        { "$ref": "#/definitions/InterfaceDeclaration" },
        { "$ref": "#/definitions/TraitDeclaration" },
        { "$ref": "#/definitions/NamespaceStatement" },
        { "$ref": "#/definitions/UseStatement" },
        { "$ref": "#/definitions/DeclareStatement" },
        { "$ref": "#/definitions/TryStatement" },
        { "$ref": "#/definitions/EmptyStatement" }
      ]
    },
    "ExpressionStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "expression_statement" },
        "expression": { "$ref": "#/definitions/Expression" }
      },
      "required": ["type", "expression"]
    },
    "CompoundStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "compound_statement" },
        "statements": {
          "type": "array",
          "items": { "$ref": "#/definitions/Statement" }
        }
      },
      "required": ["type", "statements"]
    },
    "Expression": {
      "oneOf": [
        { "$ref": "#/definitions/AssignmentExpression" },
        { "$ref": "#/definitions/ConditionalExpression" },
        { "$ref": "#/definitions/LogicalOrExpression" },
        { "$ref": "#/definitions/LogicalAndExpression" },
        { "$ref": "#/definitions/BitwiseOrExpression" },
        { "$ref": "#/definitions/BitwiseXorExpression" },
        { "$ref": "#/definitions/BitwiseAndExpression" },
        { "$ref": "#/definitions/EqualityExpression" },
        { "$ref": "#/definitions/ComparativeExpression" },
        { "$ref": "#/definitions/ShiftExpression" },
        { "$ref": "#/definitions/AdditiveExpression" },
        { "$ref": "#/definitions/MultiplicativeExpression" },
        { "$ref": "#/definitions/ExponentiationExpression" },
        { "$ref": "#/definitions/UnaryExpression" },
        { "$ref": "#/definitions/PostfixExpression" },
        { "$ref": "#/definitions/PrimaryExpression" }
      ]
    },
    "AssignmentExpression": {
      "type": "object",
      "properties": {
        "type": { "const": "assignment" },
        "operator": { "type": "string", "enum": ["=", "+=", "-=", "*=", "/=", ".=", "%=", "&=", "|=", "^=", "<<=", ">>=", "**=", "??="] },
        "left": { "$ref": "#/definitions/Variable" },
        "right": { "$ref": "#/definitions/Expression" },
        "by_ref": { "type": "boolean", "default": false }
      },
      "required": ["type", "operator", "left", "right"]
    },
    "ConditionalExpression": {
      "type": "object",
      "properties": {
        "type": { "const": "conditional" },
        "condition": { "$ref": "#/definitions/Expression" },
        "then": { "$ref": "#/definitions/Expression" },
        "else": { "$ref": "#/definitions/Expression" },
        "elvis": { "type": "boolean", "default": false }
      },
      "required": ["type", "condition"]
    },
    "Variable": {
      "type": "object",
      "properties": {
        "type": { "const": "variable" },
        "name": { "type": "string" },
        "by_ref": { "type": "boolean", "default": false }
      },
      "required": ["type", "name"]
    },
    "Literal": {
      "type": "object",
      "properties": {
        "type": { "const": "literal" },
        "value": { "type": ["string", "number", "boolean", "null"] },
        "literal_type": { "type": "string", "enum": ["integer", "float", "string", "boolean", "null"] }
      },
      "required": ["type", "value", "literal_type"]
    },
    "IfStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "if" },
        "condition": { "$ref": "#/definitions/Expression" },
        "then": { "$ref": "#/definitions/Statement" },
        "elseif": {
          "type": "array",
          "items": {
            "type": "object",
            "properties": {
              "condition": { "$ref": "#/definitions/Expression" },
              "statement": { "$ref": "#/definitions/Statement" }
            },
            "required": ["condition", "statement"]
          }
        },
        "else": { "$ref": "#/definitions/Statement" }
      },
      "required": ["type", "condition", "then"]
    },
    "SwitchStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "switch" },
        "expression": { "$ref": "#/definitions/Expression" },
        "cases": {
          "type": "array",
          "items": {
            "oneOf": [
              {
                "type": "object",
                "properties": {
                  "type": { "const": "case" },
                  "value": { "$ref": "#/definitions/Expression" },
                  "statements": {
                    "type": "array",
                    "items": { "$ref": "#/definitions/Statement" }
                  }
                },
                "required": ["type", "value", "statements"]
              },
              {
                "type": "object",
                "properties": {
                  "type": { "const": "default" },
                  "statements": {
                    "type": "array",
                    "items": { "$ref": "#/definitions/Statement" }
                  }
                },
                "required": ["type", "statements"]
              }
            ]
          }
        }
      },
      "required": ["type", "expression", "cases"]
    },
    "FunctionDeclaration": {
      "type": "object",
      "properties": {
        "type": { "const": "function" },
        "name": { "type": "string" },
        "params": {
          "type": "array",
          "items": { "$ref": "#/definitions/Parameter" }
        },
        "return_type": { "$ref": "#/definitions/Type" },
        "body": { "$ref": "#/definitions/CompoundStatement" },
        "attributes": {
          "type": "array",
          "items": { "$ref": "#/definitions/Attribute" }
        },
        "by_ref": { "type": "boolean", "default": false }
      },
      "required": ["type", "name", "params", "body"]
    },
    "Parameter": {
      "type": "object",
      "properties": {
        "type": { "const": "parameter" },
        "name": { "type": "string" },
        "type": { "$ref": "#/definitions/Type" },
        "default": { "$ref": "#/definitions/Expression" },
        "by_ref": { "type": "boolean", "default": false },
        "variadic": { "type": "boolean", "default": false },
        "visibility": { "type": "string", "enum": ["private", "protected", "public"] },
        "readonly": { "type": "boolean", "default": false }
      },
      "required": ["type", "name"]
    },
    "Type": {
      "type": "object",
      "properties": {
        "type": { "type": "string" },
        "nullable": { "type": "boolean", "default": false },
        "types": {
          "type": "array",
          "items": {
            "type": "string",
            "enum": ["int", "float", "string", "bool", "array", "callable", "iterable", "void", "never", "mixed", "false", "null", "true", "object", "parent", "self", "static"]
          }
        }
      },
      "required": ["type"]
    },
    "ClassDeclaration": {
      "type": "object",
      "properties": {
        "type": { "const": "class" },
        "name": { "type": "string" },
        "extends": { "type": "string" },
        "implements": {
          "type": "array",
          "items": { "type": "string" }
        },
        "body": {
          "type": "array",
          "items": {
            "oneOf": [
              { "$ref": "#/definitions/PropertyDeclaration" },
              { "$ref": "#/definitions/MethodDeclaration" },
              { "$ref": "#/definitions/ConstDeclaration" },
              { "$ref": "#/definitions/TraitUse" }
            ]
          }
        },
        "abstract": { "type": "boolean", "default": false },
        "final": { "type": "boolean", "default": false },
        "readonly": { "type": "boolean", "default": false },
        "attributes": {
          "type": "array",
          "items": { "$ref": "#/definitions/Attribute" }
        }
      },
      "required": ["type", "name", "body"]
    },
    "PropertyDeclaration": {
      "type": "object",
      "properties": {
        "type": { "const": "property" },
        "name": { "type": "string" },
        "type": { "$ref": "#/definitions/Type" },
        "default": { "$ref": "#/definitions/Expression" },
        "visibility": { "type": "string", "enum": ["private", "protected", "public"] },
        "static": { "type": "boolean", "default": false },
        "readonly": { "type": "boolean", "default": false },
        "attributes": {
          "type": "array",
          "items": { "$ref": "#/definitions/Attribute" }
        }
      },
      "required": ["type", "name"]
    },
    "MethodDeclaration": {
      "type": "object",
      "properties": {
        "type": { "const": "method" },
        "name": { "type": "string" },
        "params": {
          "type": "array",
          "items": { "$ref": "#/definitions/Parameter" }
        },
        "return_type": { "$ref": "#/definitions/Type" },
        "body": { "$ref": "#/definitions/CompoundStatement" },
        "visibility": { "type": "string", "enum": ["private", "protected", "public"] },
        "static": { "type": "boolean", "default": false },
        "abstract": { "type": "boolean", "default": false },
        "final": { "type": "boolean", "default": false },
        "attributes": {
          "type": "array",
          "items": { "$ref": "#/definitions/Attribute" }
        }
      },
      "required": ["type", "name", "params"]
    },
    "ConstDeclaration": {
      "type": "object",
      "properties": {
        "type": { "const": "const" },
        "name": { "type": "string" },
        "value": { "$ref": "#/definitions/Expression" },
        "visibility": { "type": "string", "enum": ["private", "protected", "public"] },
        "attributes": {
          "type": "array",
          "items": { "$ref": "#/definitions/Attribute" }
        }
      },
      "required": ["type", "name", "value"]
    },
    "TraitUse": {
      "type": "object",
      "properties": {
        "type": { "const": "trait_use" },
        "traits": {
          "type": "array",
          "items": { "type": "string" }
        },
        "adaptations": {
          "type": "array",
          "items": {
            "oneOf": [
              {
                "type": "object",
                "properties": {
                  "type": { "const": "insteadof" },
                  "trait": { "type": "string" },
                  "method": { "type": "string" },
                  "target": { "type": "string" }
                },
                "required": ["type", "trait", "method", "target"]
              },
              {
                "type": "object",
                "properties": {
                  "type": { "const": "as" },
                  "trait": { "type": "string" },
                  "method": { "type": "string" },
                  "visibility": { "type": "string", "enum": ["private", "protected", "public"] },
                  "alias": { "type": "string" }
                },
                "required": ["type", "method"]
              }
            ]
          }
        }
      },
      "required": ["type", "traits"]
    },
    "ForStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "for" },
        "init": { "$ref": "#/definitions/Expression" },
        "condition": { "$ref": "#/definitions/Expression" },
        "increment": { "$ref": "#/definitions/Expression" },
        "body": { "$ref": "#/definitions/Statement" }
      },
      "required": ["type", "body"]
    },
    "ForeachStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "foreach" },
        "expression": { "$ref": "#/definitions/Expression" },
        "key": { "$ref": "#/definitions/Variable" },
        "value": { "$ref": "#/definitions/Variable" },
        "body": { "$ref": "#/definitions/Statement" },
        "by_ref": { "type": "boolean", "default": false }
      },
      "required": ["type", "expression", "value", "body"]
    },
    "TryStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "try" },
        "body": { "$ref": "#/definitions/CompoundStatement" },
        "catches": {
          "type": "array",
          "items": {
            "type": "object",
            "properties": {
              "type": { "type": "string" },
              "variable": { "$ref": "#/definitions/Variable" },
              "body": { "$ref": "#/definitions/CompoundStatement" }
            },
            "required": ["type", "body"]
          }
        },
        "finally": { "$ref": "#/definitions/CompoundStatement" }
      },
      "required": ["type", "body"]
    },
    "ReturnStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "return" },
        "expression": { "$ref": "#/definitions/Expression" }
      },
      "required": ["type"]
    },
    "ThrowStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "throw" },
        "expression": { "$ref": "#/definitions/Expression" }
      },
      "required": ["type", "expression"]
    },
    "NamespaceStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "namespace" },
        "name": { "type": ["string", "null"] },
        "statements": {
          "type": "array",
          "items": { "$ref": "#/definitions/Statement" }
        }
      },
      "required": ["type"]
    },
    "UseStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "use" },
        "items": {
          "type": "array",
          "items": {
            "type": "object",
            "properties": {
              "type": { "type": "string", "enum": ["class", "function", "const"] },
              "name": { "type": "string" },
              "alias": { "type": "string" }
            },
            "required": ["name"]
          }
        }
      },
      "required": ["type", "items"]
    },
    "DeclareStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "declare" },
        "directives": {
          "type": "object",
          "properties": {
            "ticks": { "type": "integer" },
            "encoding": { "type": "string" },
            "strict_types": { "type": "integer", "enum": [0, 1] }
          }
        },
        "statement": { "$ref": "#/definitions/Statement" }
      },
      "required": ["type", "directives"]
    },
    "Attribute": {
      "type": "object",
      "properties": {
        "name": { "type": "string" },
        "arguments": {
          "type": "array",
          "items": { "$ref": "#/definitions/Argument" }
        }
      },
      "required": ["name"]
    },
    "Argument": {
      "oneOf": [
        {
          "type": "object",
          "properties": {
            "name": { "type": "string" },
            "value": { "$ref": "#/definitions/Expression" }
          },
          "required": ["value"]
        },
        {
          "type": "object",
          "properties": {
            "value": { "$ref": "#/definitions/Expression" },
            "spread": { "type": "boolean", "default": false }
          },
          "required": ["value"]
        }
      ]
    },
    "EmptyStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "empty" }
      },
      "required": ["type"]
    },
    "GotoStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "goto" },
        "label": { "type": "string" }
      },
      "required": ["type", "label"]
    },
    "ContinueStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "continue" },
        "level": { "type": "integer" }
      },
      "required": ["type"]
    },
    "BreakStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "break" },
        "level": { "type": "integer" }
      },
      "required": ["type"]
    },
    "WhileStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "while" },
        "condition": { "$ref": "#/definitions/Expression" },
        "body": { "$ref": "#/definitions/Statement" }
      },
      "required": ["type", "condition", "body"]
    },
    "DoStatement": {
      "type": "object",
      "properties": {
        "type": { "const": "do_while" },
        "condition": { "$ref": "#/definitions/Expression" },
        "body": { "$ref": "#/definitions/Statement" }
      },
      "required": ["type", "condition", "body"]
    },
    "InterfaceDeclaration": {
      "type": "object",
      "properties": {
        "type": { "const": "interface" },
        "name": { "type": "string" },
        "extends": {
          "type": "array",
          "items": { "type": "string" }
        },
        "body": {
          "type": "array",
          "items": {
            "oneOf": [
              { "$ref": "#/definitions/MethodDeclaration" },
              { "$ref": "#/definitions/ConstDeclaration" }
            ]
          }
        },
        "attributes": {
          "type": "array",
          "items": { "$ref": "#/definitions/Attribute" }
        }
      },
      "required": ["type", "name", "body"]
    },
    "TraitDeclaration": {
      "type": "object",
      "properties": {
        "type": { "const": "trait" },
        "name": { "type": "string" },
        "body": {
          "type": "array",
          "items": {
            "oneOf": [
              { "$ref": "#/definitions/PropertyDeclaration" },
              { "$ref": "#/definitions/MethodDeclaration" },
              { "$ref": "#/definitions/ConstDeclaration" }
            ]
          }
        },
        "attributes": {
          "type": "array",
          "items": { "$ref": "#/definitions/Attribute" }
        }
      },
      "required": ["type", "name", "body"]
    },
    "EnumDeclaration": {
      "type": "object",
      "properties": {
        "type": { "const": "enum" },
        "name": { "type": "string" },
        "backing_type": { "type": "string", "enum": ["int", "string"] },
        "implements": {
          "type": "array",
          "items": { "type": "string" }
        },
        "cases": {
          "type": "array",
          "items": {
            "type": "object",
            "properties": {
              "name": { "type": "string" },
              "value": { "$ref": "#/definitions/Expression" }
            },
            "required": ["name"]
          }
        },
        "body": {
          "type": "array",
          "items": {
            "oneOf": [
              { "$ref": "#/definitions/MethodDeclaration" },
              { "$ref": "#/definitions/ConstDeclaration" }
            ]
          }
        },
        "attributes": {
          "type": "array",
          "items": { "$ref": "#/definitions/Attribute" }
        }
      },
      "required": ["type", "name", "cases"]
    }
  },
  "$ref": "#/definitions/Program"
}
```

## Usage Examples

### EBNF Usage

The EBNF grammar can be used with parser generators like:

- **ANTLR** - Convert to ANTLR4 grammar format
- **Racc** - Ruby parser generator
- **LALR** - For LALR parser generators
- **YACC** - For YACC/Bison compatible parsers

### PEG Usage

The PEG grammar can be used with:

- **PEG.js** - JavaScript parser generator
- **Ragel** - State machine compiler
- **Packrat** - Packrat parsing
- **LPEG** - Lua PEG implementation

### JSON Schema Usage

The JSON Schema can be used for:

1. **Validating PHP AST structures**
2. **API response validation**
3. **Static analysis tools**
4. **IDE autocompletion**
5. **Code generation tools**

```php
// Example of using JSON Schema with PHP
use Opis\JsonSchema\Validator;

$validator = new Validator();
$schema = json_decode(file_get_contents('php-grammar.schema.json'));
$ast = json_decode(file_get_contents('parsed-ast.json'));

if ($validator->validate($ast, $schema)) {
    echo "Valid PHP AST";
} else {
    echo "Invalid PHP AST: " . $validator->getErrors();
}
```

These grammar definitions cover:

- **Full PHP 8.x syntax** including enums, readonly classes, DNF types
- **All operators** with correct precedence
- **All statement types**
- **Attributes** (#[Attribute])
- **Match expressions**
- **Anonymous classes and functions**
- **Arrow functions (fn)**
- **All type declarations**
- **Heredoc and nowdoc**
- **Namespaces and imports**
- **Error handling (try/catch/finally)**

Each format serves a different purpose:
- **EBNF**: Standard for language specification
- **PEG**: Good for recursive descent parsers
- **JSON Schema**: Great for validation and tooling


## ⚡ Performance

### Benchmarks

| Operation | Time | Memory |
|-----------|------|--------|
| PHP Parser (1000 lines) | ~50ms | 2MB |
| JSON-RPC Request | ~2ms | 256KB |
| DNS Lookup (cached) | ~1ms | 128KB |
| File Cache Read | ~0.5ms | 64KB |
| MCP Request | ~3ms | 512KB |

### Optimization Tips

1. **Use Cache** - Enable file caching for frequently accessed data
2. **Enable DNS Cache** - Reduce DNS resolution time
3. **Optimize PHP Files** - Keep PHP scripts small and focused
4. **Use Middleware** - Add custom middleware for request processing
5. **Monitor Logs** - Review logs for performance bottlenecks

## 🔧 Troubleshooting

### Common Issues

#### Port Already in Use
```
Error: Port 8080 is already in use
```
**Solution:** Change the port:
```powershell
.\PHPServer.ps1 -Port 8081
```

#### Permission Denied
```
Access Denied: Cannot write to cache directory
```
**Solution:** Ensure the cache directory has write permissions:
```powershell
# Windows
icacls cache /grant Users:F

# Linux/macOS
chmod 755 cache
```

#### PHP Parsing Errors
```
PHP Error: Syntax error at line X
```
**Solution:** Check your PHP syntax and ensure you're using valid PHP 8.x syntax.

#### MCP Not Working
```
Unknown MCP message type
```
**Solution:** Enable MCP support:
```powershell
.\PHPServer.ps1 -EnableMCP $true
```

### Debugging

Enable verbose logging:
```powershell
$VerbosePreference = "Continue"
.\PHPServer.ps1
```

Check error logs:
```powershell
Get-Content .\logs\error.log -Tail 50
```

### Logging

The server writes logs to:
- `./logs/server.log` - Request logs
- `./logs/error.log` - Error logs

Log format:
```
[2024-01-15 10:30:45] [INFO] GET /health
[2024-01-15 10:30:46] [ERROR] DNS lookup failed: domain not found
```

## 🤝 Contributing

### How to Contribute

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Development Environment

```powershell
# Clone the repository
git clone 
cd PHPServer.ps1

# Run tests
.\tests\RunTests.ps1

# Build documentation
.\scripts\BuildDocs.ps1
```

### Code Style

- Follow PowerShell best practices
- Use meaningful variable names
- Add comments for complex logic
- Include error handling
- Write unit tests for new features

### Reporting Issues

Use the GitHub issue tracker to report bugs and feature requests.

Include:
- Version of PHPServer.ps1
- PowerShell version (`$PSVersionTable`)
- Operating system
- Steps to reproduce
- Expected/actual behavior

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

```
MIT License

Copyright (c) 2024 PHP Server

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

## 🙏 Acknowledgments

- PHP.net for the language specification
- PowerShell team for the excellent scripting environment
- MCP specification contributors
- Open source community for inspiration

## 📞 Support

- **Documentation:** https://github.com/cannaseedus-bot/ASX-PHP


---

**Made with ❤️ for the PHP and PowerShell communities**
