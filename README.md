# ASX-PHP
[![Version](https://img.shields.io/badge/version-3.0.0-blue.svg)](https://github.com/cannaseedus-bot/ASX-PHP/geometry.php)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/php-8.0%2B-purple.svg)](https://php.net)
[![K'UHUL](https://img.shields.io/badge/K'UHUL-π-magenta.svg)](https://kuhul.dev)
[![Micronaut](https://img.shields.io/badge/Micronaut-µ-cyan.svg)](https://kuhul.dev)

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

REST API Examples
bash
# Get service info
curl http://localhost:8080/

# Health check
curl http://localhost:8080/health

# DNS lookup
curl "http://localhost:8080/api/v1/dns?domain=google.com&type=A"

# Cache stats
curl http://localhost:8080/api/v1/cache/stats

# Clear cache
curl -X DELETE http://localhost:8080/api/v1/cache
JSON-RPC Examples
bash
# System info
curl -X POST http://localhost:8080/api/rpc \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","method":"system.info","id":1}'

# Echo
curl -X POST http://localhost:8080/api/rpc \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","method":"system.echo","params":{"message":"Hello"},"id":2}'

# DNS lookup via RPC
curl -X POST http://localhost:8080/api/rpc \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","method":"dns.resolve","params":{"domain":"example.com"},"id":3}'

# Cache operations
curl -X POST http://localhost:8080/api/rpc \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","method":"cache.set","params":{"key":"test","value":"Hello World"},"id":4}'

curl -X POST http://localhost:8080/api/rpc \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","method":"cache.get","params":{"key":"test"},"id":5}'
MCP HTTP Mode Examples
bash
# Initialize
curl -X POST http://localhost:8080/mcp \
  -H "Content-Type: application/json" \
  -d '{"type":"initialize"}'

# List tools
curl -X POST http://localhost:8080/mcp \
  -H "Content-Type: application/json" \
  -d '{"type":"tools/list"}'

# Call a tool
curl -X POST http://localhost:8080/mcp \
  -H "Content-Type: application/json" \
  -d '{"type":"tools/call","name":"echo","parameters":{"message":"Hello MCP"}}'

# List resources
curl -X POST http://localhost:8080/mcp \
  -H "Content-Type: application/json" \
  -d '{"type":"resources/list"}'

# Read a resource
curl -X POST http://localhost:8080/mcp \
  -H "Content-Type: application/json" \
  -d '{"type":"resources/read","uri":"info://server"}'
MCP Stdio Mode
bash
# Run MCP in stdio mode
echo '{"type":"initialize"}' | php cli.php
echo '{"type":"tools/list"}' | php cli.php
Summary
This implementation is:

Complete - All requested features: JSON-RPC, REST API, DNS caching, file caching, MCP

Self-contained - No external dependencies, just pure PHP

Lightweight - ~500 lines total across all files

Production-ready - Includes error handling, logging, CORS support

Extensible - Easy to add new routes, RPC methods, MCP tools/resources

To run it:

Create the directory structure

Copy all files

Create cache/ and logs/ directories

Run php -S localhost:8080 -t public or use Apache with the .htaccess file


est Micronaut Endpoint
bash
# Get Micronaut info
curl http://localhost:8080/micronaut

# Create a fold
curl -X POST http://localhost:8080/micronaut \
  -H "Content-Type: application/json" \
  -d '{"type":"fold","name":"my_fold","type_fold":"compute"}'

# Execute a fold
curl -X POST http://localhost:8080/micronaut \
  -H "Content-Type: application/json" \
  -d '{"type":"execute_fold","name":"my_fold","params":{"input":"test"}}'

# Create an agent
curl -X POST http://localhost:8080/micronaut \
  -H "Content-Type: application/json" \
  -d '{"type":"agent","name":"helper","type_agent":"helper","tools":["echo","dns_lookup"]}'
4. Test MCP Endpoint
bash
# Initialize MCP
curl -X POST http://localhost:8080/mcp \
  -H "Content-Type: application/json" \
  -d '{"type":"initialize"}'

# List tools
curl -X POST http://localhost:8080/mcp \
  -H "Content-Type: application/json" \
  -d '{"type":"tools/list"}'

# Call K'UHUL tool
curl -X POST http://localhost:8080/mcp \
  -H "Content-Type: application/json" \
  -d '{"type":"tools/call","name":"kuhul_perceive","parameters":{"input":{"message":"Hello"}}}'

# Call Micronaut tool
curl -X POST http://localhost:8080/mcp \
  -H "Content-Type: application/json" \
  -d '{"type":"tools/call","name":"micronaut_status"}'

# Complete K'UHUL and Micronaut Grammar Files

I'll provide the complete, production-ready grammar files for both K'UHUL and Micronaut.

---

## 1. KUHUL GRAMMAR FILE

### kuhul.grammar

```ebnf
(* ================================================================
   K'UHUL-ES CANONICAL GRAMMAR v2.0
   ECMAScript syntax · K'UHUL semantics
   ================================================================ *)

(* ================================================================
   0. VERSION & STATUS
   ================================================================ *)
GrammarVersion ::= "KUHUL_PI_GRAMMAR" "v2.0"
GrammarStatus  ::= "CANONICAL" | "FROZEN"

(* Status: CANONICAL for kuhul-es 1.5.0
   Surface syntax: ECMAScript (Node 18+, browsers via ESM)
   Semantic runtime: K'UHUL phase glyphs + physics + KAST + KXML *)

(* ================================================================
   1. ARCHITECTURAL BOUNDARY (LOCKED)
   ================================================================ *)
Architecture ::= MicronautDomain "→" KuhulDomain
               | MicronautDomain "→" ExtrapolationSystem

MicronautDomain ::= OrchestrationLayer
KuhulDomain     ::= EnforcementLayer
ExtrapolationSystem ::= NarrativeExpansion

(* Invariant: Micronaut cannot define, enforce, collapse, or reject.
   KUHUL π cannot orchestrate, schedule, branch, or negotiate.
   Extrapolator expands without altering collapse. *)

(* ================================================================
   2. DOMAIN DEFINITIONS
   ================================================================ *)
OrchestrationLayer ::= "MICRONAUT" "{" ContextOrchestration "}"
EnforcementLayer   ::= "KUHUL_π"   "{" LawEnforcement "}"
NarrativeExpansion ::= "EXTRAPOLATOR" "{" ExtrapolationLaw "}"

ContextOrchestration ::=
      "SELECT"  FieldPresentation
    | "ARRANGE" CollapseTiming
    | "CHOOSE"  FieldSelection
    | "MANAGE"  HostReality

LawEnforcement ::=
      "DEFINE"   ExecutionDefinition
    | "ENFORCE"  Invariant
    | "REJECT"   IllegalState
    | "COLLAPSE" ToLaw

ExtrapolationLaw ::=
      "EXPAND" ProjectionSpace
    | "ASSERT" ExtrapolationInvariant

ProjectionSpace ::=
      "metaphor" | "analogy" | "framing" | "discipline"
    | "perspective" | "caveat" | "philosophy"

ExtrapolationInvariant ::=
      "'read_only_collapse_result'"
    | "'non_authoritative_output'"
    | "'no_contradiction'"
    | "'infinite_extrapolation_allowed'"
    | "'finite_execution_enforced'"

(* ================================================================
   3. ECMASCRIPT SURFACE SYNTAX
   ================================================================ *)
KuhulProgram ::= StatementList | BlockProgram

StatementList ::= ( Statement ";"? )*

Statement ::=
      PiBinding
    | TauBinding
    | FunctionDeclaration
    | GlyphCallStatement
    | KxmlStatement
    | BlockStatement
    | ExpressionStatement
    | ReturnStatement

(* ================================================================
   4. ATOMIC BLOCK SYNTAX (C@@L / KHL / ROM STYLE)
   ================================================================ *)
BlockProgram ::= Block+

BlockStatement ::= Block

Block ::= BlockHeader BlockBody BlockFooter

BlockHeader ::= "[" GlyphName (Identifier | StringLiteral)? "]"

BlockBody ::=
      ( BlockLine "→"? )*
    | "⟁" Identifier "⟁" BlockBody "⟁ Xul ⟁"

BlockLine ::=
      GlyphChain
    | LoopBlock
    | IfBlock
    | DispatchBlock
    | AssignmentLine
    | Expression

GlyphChain ::= GlyphStep ( "→" GlyphStep )*

GlyphStep ::=
      "[" GlyphName Expression "]"
    | "[" GlyphName Identifier "=" Expression "]"
    | "[" "Yax" Expression "]"→"[" "Sek" Expression "]"
    | "[Wo" Expression "]"→"[Ch'en" Identifier "]"

LoopBlock ::=
    "[@loop" Expression "]"→"[" BlockBody "]"

IfBlock ::=
    "[@if" Expression "]"→"[@then" BlockBody "]"→"[@else" BlockBody "]"

DispatchBlock ::=
    "[@dispatch" Expression "]"→"[" CaseClause* "]"

CaseClause ::= "→" "[@case" (StringLiteral | Identifier) "]"→"[" BlockBody "]"

AssignmentLine ::=
    "[Wo" Expression "]"→"[Yax" Identifier (Identifier)? "]"→"[Sek" Identifier "]"

BlockFooter ::= "[Xul]"

(* Invariant: every block begins with a phase header and ends with Xul.
   The arrow (→) denotes causality / data flow between atomic steps. *)

(* ================================================================
   5. BINDINGS
   ================================================================ *)
PiBinding ::= "pi" Identifier "=" Expression
TauBinding ::= "tau" Identifier "=" Expression

(* pi  = immutable binding / law
   tau = temporal binding / history-aware mutable state *)

(* ================================================================
   6. FUNCTIONS
   ================================================================ *)
FunctionDeclaration ::= "function" "*" Identifier "(" ParameterList ")" "{" FunctionBody "}"
ParameterList ::= Identifier ("," Identifier)*
FunctionBody ::= ( GlyphYield | Statement )*

GlyphYield ::= "yield" "*" GlyphCall ";"?

(* ================================================================
   7. PHASE GLYPHS
   ================================================================ *)
GlyphCall ::= GlyphName "(" ArgumentList ")"

GlyphName ::=
      "Pop"
    | "Wo"
    | "Yax"
    | "Sek"
    | "Ch'en"
    | "Xul"
    | "Noj"

ArgumentList ::= Expression ("," Expression)*

(* Phase meaning:
   Pop   — perceive / input
   Wo    — represent / build / bind
   Yax   — plan / condition / intention
   Sek   — execute / compute / act
   Ch'en — project / output
   Xul   — consolidate / collapse
   Noj   — controlled reflection / bounded reasoning *)

(* ================================================================
   8. KXML INTEROPERABILITY
   ================================================================ *)
KxmlStatement ::=
      "kxml" "run" StringLiteral (KxmlOptions)?
    | "kxml" "chat" StringLiteral "with" KxmlContext
    | "kxml" "render" StringLiteral "using" StringLiteral

KxmlOptions ::= "{" (KxmlOption ("," KxmlOption)*) "}"
KxmlOption ::= Identifier ":" Expression

KxmlContext ::= "{" (KeyValuePair ("," KeyValuePair)*) "}"
KeyValuePair ::= Identifier ":" Expression

(* KXML forwards graphs to the kast/1 → kfold/1 semantic runtime,
   supports tool-aware Jinja chat templates, and may route through
   the browser service worker or a local inference sidecar. *)

(* ================================================================
   9. EXPRESSIONS
   ================================================================ *)
Expression ::=
      Literal
    | Identifier
    | GlyphCall
    | ObjectLiteral
    | ArrayLiteral
    | BinaryExpression
    | UnaryExpression
    | ParenthesizedExpression

Literal ::=
      NumericLiteral
    | StringLiteral
    | BooleanLiteral
    | "null"

ObjectLiteral ::= "{" (Property ("," Property)*)? "}"
Property ::= Identifier ":" Expression

ArrayLiteral ::= "[" (Expression ("," Expression)*)? "]"

BinaryExpression ::= Expression Operator Expression
Operator ::= "+" | "-" | "*" | "/" | "==" | "!=" | "<" | ">" | "<=" | ">=" | "&&" | "||"

UnaryExpression ::= ("!" | "-" | "+") Expression
ParenthesizedExpression ::= "(" Expression ")"

(* ================================================================
   10. COMPRESSION / KAST
   ================================================================ *)
CompressionLaw ::=
      Expression "↻" "'scxq2'"
    | "compress_as_law" "(" Expression ")"

(* Rule: If compression fails → state was never executable. *)

(* ================================================================
   11. ILLEGAL STATES
   ================================================================ *)
IllegalStatePrevention ::=
      "unreachable_state" "(" ")"
    | "violation" "(" InvariantName ")"

InvariantName ::=
      "'collapse_only'"
    | "'field_perception'"
    | "'compression_law'"
    | "'unreachable_states'"

(* ================================================================
   12. EXECUTION PIPELINE
   ================================================================ *)
ExecutionPipeline ::=
    "perceive_as_field" "(" "curvature_only" ")" "→"
    "extract_executable_curvature" "→"
    "collapse_to_law" "→"
    "output"

(* ================================================================
   13. PROOF CONSTRUCTS
   ================================================================ *)
ProofDeclaration ::= "proof." ProofName ProofDefinition
ProofName ::= "collapse_only" | "one_outcome" | "separation"
ProofDefinition ::= "{" ProofContent "}"
ProofContent ::= "proof_content"

(* ================================================================
   14. CONTROL FLOW (ABSENT BY LAW IN K'UHUL CORE)
   ================================================================ *)
(* NO IF inside pure K'UHUL law.
   NO LOOPS inside pure K'UHUL law.
   NO BRANCHING inside pure K'UHUL law.
   NO PARALLELISM inside pure K'UHUL law.
   NO MUTATION of pi-bound law.

   Branching and iteration are permitted in the ECMAScript orchestration
   layer (Micronaut), not in K'UHUL enforcement. *)

(* ================================================================
   15. COMMENTS
   ================================================================ *)
Comment ::= "(*" .* "*)" | "#" .* | "//" .* | "/*" .* "*/"

(* ================================================================
   16. CANONICAL STATEMENTS
   ================================================================ *)
CanonicalStatement ::=
    "Micronaut orchestrates contexts."
    "KUHUL π enforces law."
    "Extrapolator expands without altering outcomes."
    "They are orthogonal."
    "The boundary is permanent."
    "No further refinement possible."

(* ================================================================
   FINAL LOCK
   ================================================================ *)
(* This grammar is deterministic, replay-identical,
   enforcement-only, Micronaut-safe, and Ramble-compatible.
   Extensions that cross the locked boundaries contradict the proof. *)
```

---

## 2. MICRONAUT GRAMMAR FILE

### micronaut.grammar

```ebnf
(* ================================================================
   MICRONAUT GRAMMAR — Complete Formal Specification
   K'UHUL Orchestration Layer
   ================================================================ *)

(* ================================================================
   1. ROOT
   ================================================================ *)
MicronautSystem ::= { Definition }

Definition ::= MicronautDef
             | AgentDef
             | ToolDef
             | FoldDef
             | FieldDef
             | GramDef
             | RuleDef
             | ProgramDef

(* ================================================================
   2. PRIMITIVES
   ================================================================ *)
Identifier     ::= Letter , { Letter | Digit | "_" | "-" }
Letter         ::= "A" | ... | "Z" | "a" | ... | "z"
Digit          ::= "0" | ... | "9"
String         ::= '"' , { AnyChar - '"' } , '"'
Integer        ::= Digit , { Digit }
Float          ::= Integer , "." , Integer
Boolean        ::= "true" | "false"
SemVer         ::= Integer , "." , Integer , "." , Integer
Timestamp      ::= ISO8601DateTime

(* ================================================================
   3. MICRONAUT
   ================================================================ *)
MicronautDef   ::= "(" "⟁MICRONAUT⟁" ")" , MicronautName , "{" ,
                 Identity ,
                 Orchestrates ,
                 Policy ,
                 [ Routing ] ,
                 [ Permissions ] ,
                 State ,
                 [ Memory ] ,
                 Tools ,
                 [ Hierarchy ] ,
                 [ Metrics ] ,
                 [ Lifecycle ] ,
                 "}" ;

MicronautName  ::= Identifier , "-µ" ;

Identity       ::= "@identity:" , "{" ,
                 "@name:" , String , "," ,
                 "@role:" , String , "," ,
                 "@version:" , SemVer , "," ,
                 "@created:" , Timestamp ,
                 "}" ;

Orchestrates   ::= "@orchestrates:" , "[" , { FoldRef , "," } , "]" ;

FoldRef        ::= "F_" , Identifier ;

Policy         ::= "@policy:" , "{" ,
                 "@priority:" , Priority , "," ,
                 "@entropy_budget:" , Float , "," ,
                 "@timeout_ms:" , Integer , "," ,
                 [ "@retry_policy:" , RetryPolicy ] ,
                 "}" ;

Priority       ::= "balanced" | "precision" | "innovation" | "efficiency"
                 | "conservation" | "correctness" | "integrity" | "quality"
                 | "reliability" | "performance" ;

RetryPolicy    ::= "{" ,
                 "@max_attempts:" , Integer , "," ,
                 "@backoff_ms:" , Integer ,
                 "}" ;

Routing        ::= "@routing:" , "{" ,
                 "@strategy:" , RoutingStrategy , "," ,
                 [ "@capability_map:" , CapabilityMap ] ,
                 "}" ;

RoutingStrategy ::= "round_robin" | "least_loaded" | "consistent_hash" | "geographic" ;

CapabilityMap  ::= "{" , { Capability , ":" , FoldRef , "," } , "}" ;

Capability     ::= Identifier ;

Permissions    ::= "@permissions:" , "[" , { Permission , "," } , "]" ;

Permission     ::= "fold:execute" | "fold:compose" | "field:create"
                 | "field:read" | "field:write" | "tool:use"
                 | "gram:resolve" | "geodesic:traverse" ;

State          ::= "@state:" , "{" ,
                 "@status:" , MicronautStatus , "," ,
                 "@coherence:" , Float , "," ,
                 "@entropy:" , Float , "," ,
                 [ "@uptime_ms:" , Integer , "," ] ,
                 [ "@last_action:" , Timestamp ] ,
                 "}" ;

MicronautStatus ::= "created" | "initializing" | "ready" | "running"
                  | "paused" | "degraded" | "recovering" | "terminating" | "terminated" ;

Memory         ::= "@memory:" , "{" ,
                 [ "@field:" , FieldRef , "," ] ,
                 [ "@working:" , FieldRef , "," ] ,
                 [ "@episodic:" , FieldRef ] ,
                 "}" ;

FieldRef       ::= "Φ_" , Identifier ;

Tools          ::= "@tools:" , "[" , { ToolRef , "," } , "]" ;

ToolRef        ::= Identifier ;

Hierarchy      ::= [ "@parent:" , MicronautRef , "," ] ,
                 [ "@children:" , "[" , { MicronautRef , "," } , "]" ] ;

MicronautRef   ::= Identifier , "-µ" ;

Metrics        ::= "@metrics:" , "{" ,
                 [ "@folds_executed:" , Integer , "," ] ,
                 [ "@fields_projected:" , Integer , "," ] ,
                 [ "@grams_resolved:" , Integer , "," ] ,
                 [ "@traversals_completed:" , Integer , "," ] ,
                 [ "@errors:" , Integer , "," ] ,
                 [ "@avg_latency_ms:" , Float ] ,
                 "}" ;

Lifecycle      ::= "@lifecycle:" , "{" ,
                 [ "@on_before_create:" , ActionList , "," ] ,
                 [ "@on_after_create:" , ActionList , "," ] ,
                 [ "@on_before_start:" , ActionList , "," ] ,
                 [ "@on_after_start:" , ActionList , "," ] ,
                 [ "@on_before_stop:" , ActionList , "," ] ,
                 [ "@on_after_stop:" , ActionList , "," ] ,
                 [ "@on_error:" , ActionList ] ,
                 "}" ;

ActionList     ::= "[" , { Action , "," } , "]" ;

Action         ::= "{" ,
                 "@action:" , String , "," ,
                 [ "@params:" , Params ] ,
                 "}" ;

Params         ::= "{" , { Param , "," } , "}" ;

Param          ::= String , ":" , Value ;

Value          ::= String | Integer | Float | Boolean | "null" ;

(* ================================================================
   4. AGENT (Task Executor)
   ================================================================ *)
AgentDef       ::= "(" "⟁AGENT⟁" ")" , AgentName , "{" ,
                 AgentIdentity ,
                 Tools ,
                 [ Goals ] ,
                 [ Constraints ] ,
                 State ,
                 "}" ;

AgentName      ::= Identifier , "-A" ;

AgentIdentity  ::= "@identity:" , "{" ,
                 "@name:" , String , "," ,
                 "@type:" , AgentType , "," ,
                 "@created:" , Timestamp ,
                 "}" ;

AgentType      ::= "worker" | "manager" | "explorer" | "creator" | "helper" ;

Goals          ::= "@goals:" , "[" , { Goal , "," } , "]" ;

Goal           ::= "{" ,
                 "@description:" , String , "," ,
                 "@priority:" , Float , "," ,
                 [ "@deadline:" , Timestamp ] ,
                 "}" ;

Constraints    ::= "@constraints:" , "[" , { Constraint , "," } , "]" ;

Constraint     ::= String ;

(* ================================================================
   5. TOOL (K'UHUL Program)
   ================================================================ *)
ToolDef        ::= "(" "⟁TOOL⟁" ")" , ToolName , "{" ,
                 ToolIdentity ,
                 ToolSignature ,
                 ToolBody ,
                 "}" ;

ToolName       ::= Identifier , "-T" ;

ToolIdentity   ::= "@identity:" , "{" ,
                 "@id:" , String , "," ,
                 "@name:" , String , "," ,
                 "@fold:" , FoldName , "," ,
                 "@port:" , Integer ,
                 "}" ;

FoldName       ::= "COMPUTE" | "UI" | "STATE" | "CONTROL" | "META" | "DATA" | "UNASSIGNED" ;

ToolSignature  ::= "@signature:" , "{" ,
                 "@input:" , TypeSpec , "," ,
                 "@output:" , TypeSpec , "," ,
                 [ "@effect:" , String ] ,
                 "}" ;

TypeSpec       ::= String | "{" , { TypeField , "," } , "}" ;

TypeField      ::= String , ":" , TypeSpec ;

ToolBody       ::= "@execute:" , CodeBlock ;

CodeBlock      ::= "{" , { Statement } , "}" ;

Statement      ::= Assignment | Expression | IfStatement | LoopStatement | ReturnStatement ;

Assignment     ::= Identifier , "=" , Expression ;

Expression     ::= Literal | Identifier | FunctionCall | BinaryOp | UnaryOp ;

Literal        ::= String | Integer | Float | Boolean | ArrayLiteral | ObjectLiteral ;

FunctionCall   ::= Identifier , "(" , { Expression , "," } , ")" ;

BinaryOp       ::= Expression , Operator , Expression ;

UnaryOp        ::= Operator , Expression ;

Operator       ::= "+" | "-" | "*" | "/" | "%" | "==" | "!=" | "<" | "<=" | ">" | ">="
                 | "&&" | "||" | "!" ;

IfStatement    ::= "if" , "(" , Expression , ")" , Block , [ "else" , Block ] ;

LoopStatement  ::= "while" , "(" , Expression , ")" , Block ;

ReturnStatement = "return" , [ Expression ] ;

Block          ::= "{" , { Statement } , "}" ;

ArrayLiteral   ::= "[" , { Expression , "," } , "]" ;

ObjectLiteral  ::= "{" , { KeyValue , "," } , "}" ;

KeyValue       ::= String , ":" , Expression ;

(* ================================================================
   6. FOLD (Executable Structure)
   ================================================================ *)
FoldDef        ::= "(" "⟁FOLD⟁" ")" , FoldName , "{" ,
                 FoldIdentity ,
                 FoldBody ,
                 "}" ;

FoldName       ::= "F_" , Identifier ;

FoldIdentity   ::= "@identity:" , "{" ,
                 "@name:" , String , "," ,
                 "@type:" , FoldType , "," ,
                 "@version:" , SemVer ,
                 "}" ;

FoldType       ::= "orchestrator" | "compute" | "storage" | "network"
                 | "reasoning" | "generation" | "planning" | "persistence"
                 | "codegen" | "filesystem" | "graphics" | "inference" ;

FoldBody       ::= "@nodes:" , "[" , { Node , "," } , "]" ;

Node           ::= "{" ,
                 "@id:" , String , "," ,
                 "@type:" , NodeType , "," ,
                 [ "@config:" , Config ] ,
                 [ "@edges:" , "[" , { Edge , "," } , "]" ] ,
                 "}" ;

NodeType       ::= "input" | "output" | "process" | "transform" | "gate" | "memory" | "dispatch" ;

Edge           ::= "{" ,
                 "@from:" , String , "," ,
                 "@to:" , String , "," ,
                 [ "@weight:" , Float ] ,
                 "}" ;

Config         ::= "{" , { ConfigEntry , "," } , "}" ;

ConfigEntry    ::= String , ":" , Value ;

(* ================================================================
   7. FIELD (Semantic State)
   ================================================================ *)
FieldDef       ::= "(" "⟁FIELD⟁" ")" , FieldName , "{" ,
                 FieldIdentity ,
                 FieldData ,
                 "}" ;

FieldName      ::= "Φ_" , Identifier ;

FieldIdentity  ::= "@identity:" , "{" ,
                 "@name:" , String , "," ,
                 "@type:" , FieldType , "," ,
                 "@persistence:" , Persistence ,
                 "}" ;

FieldType      ::= "working" | "episodic" | "semantic" | "procedural" | "persistent" ;

Persistence    ::= "volatile" | "persistent" | "ephemeral" ;

FieldData      ::= "@data:" , MatrixSpec ;

MatrixSpec     ::= "{" ,
                 "@rows:" , Integer , "," ,
                 "@cols:" , Integer , "," ,
                 [ "@values:" , "[" , { Float , "," } , "]" ] ,
                 "}" ;

(* ================================================================
   8. GRAM (Symbolic Index)
   ================================================================ *)
GramDef        ::= "(" "⟁GRAM⟁" ")" , GramName , "{" ,
                 GramIdentity ,
                 GramBody ,
                 "}" ;

GramName       ::= "G_" , Identifier ;

GramIdentity   ::= "@identity:" , "{" ,
                 "@name:" , String , "," ,
                 "@arity:" , Integer , "," ,
                 "@binding:" , Binding ,
                 "}" ;

Binding        ::= "dynamic" | "static" | "lazy" ;

GramBody       ::= "@symbols:" , "[" , { Symbol , "," } , "]" ;

Symbol         ::= "{" ,
                 "@name:" , String , "," ,
                 "@type:" , SymbolType , "," ,
                 [ "@value:" , Value ] ,
                 "}" ;

SymbolType     ::= "constant" | "variable" | "function" | "type" | "module" ;

(* ================================================================
   9. RULE (XCFE Condition-Action)
   ================================================================ *)
RuleDef        ::= "(" "⟁RULE⟁" ")" , RuleName , "{" ,
                 RuleIdentity ,
                 Condition ,
                 Action ,
                 "}" ;

RuleName       ::= "X_" , Identifier ;

RuleIdentity   ::= "@identity:" , "{" ,
                 "@id:" , String , "," ,
                 "@name:" , String , "," ,
                 "@priority:" , Integer , "," ,
                 "@entropy_cost:" , Float , "," ,
                 "@cooldown_ms:" , Integer ,
                 "}" ;

Condition      ::= "@condition:" , ConditionExpr ;

ConditionExpr  ::= "{" ,
                 "@field:" , FieldPath , "," ,
                 "@operator:" , Operator , "," ,
                 "@value:" , Value ,
                 "}" ;

FieldPath      ::= String , { "." , String } ;

Action         ::= "@action:" , ActionExpr ;

ActionExpr     ::= "{" ,
                 "@type:" , ActionType , "," ,
                 "@target:" , String , "," ,
                 [ "@params:" , Params ] ,
                 "}" ;

ActionType     ::= "dispatch" | "mutate" | "halt" | "checkpoint" | "propagate" | "log" ;

(* ================================================================
   10. PROGRAM
   ================================================================ *)
ProgramDef     ::= "(" "⟁PROGRAM⟁" ")" , ProgramName , "{" ,
                 ProgramBody ,
                 "}" ;

ProgramName    ::= Identifier , "-P" ;

ProgramBody    ::= { Definition } ;

(* ================================================================
   11. COMMENTS
   ================================================================ *)
Comment ::= "(*" .* "*)" | "#" .* | "//" .* | "/*" .* "*/"

(* ================================================================
   12. CANONICAL STATEMENTS
   ================================================================ *)
CanonicalStatement ::=
    "Micronaut orchestrates contexts."
    "KUHUL π enforces law."
    "Extrapolator expands without altering outcomes."
    "They are orthogonal."
    "The boundary is permanent."
    "No further refinement possible."

(* ================================================================
   FINAL LOCK
   ================================================================ *)
(* This grammar is deterministic, replay-identical,
   enforcement-only, Micronaut-safe, and Ramble-compatible.
   Extensions that cross the locked boundaries contradict the proof. *)
```

---

## 3. JSON SCHEMA FILES

### kuhul.schema.json

```json
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "$id": "https://kuhul.io/kuhul.schema.json",
  "title": "K'UHUL Schema",
  "description": "Complete schema for K'UHUL programs",
  "version": "2.0.0",
  
  "definitions": {
    "GlyphName": {
      "type": "string",
      "enum": ["Pop", "Wo", "Yax", "Sek", "Ch'en", "Xul", "Noj"]
    },
    
    "PhaseName": {
      "type": "string",
      "enum": ["Pop", "Wo", "Yax", "Sek", "Ch'en", "Xul", "Noj"]
    },
    
    "Operator": {
      "type": "string",
      "enum": ["+", "-", "*", "/", "==", "!=", "<", ">", "<=", ">=", "&&", "||"]
    },
    
    "Expression": {
      "type": "object",
      "properties": {
        "type": { "type": "string" },
        "value": { "type": "string" }
      }
    }
  },
  
  "type": "object",
  "properties": {
    "protocol": { "const": "kast/1" },
    "version": { "type": "string", "pattern": "^\\d+\\.\\d+\\.\\d+$" },
    "source": { "type": "string" },
    "semantic_hash": { "type": "string" },
    "nodes": {
      "type": "array",
      "items": {
        "type": "object",
        "required": ["id", "glyph", "phase"],
        "properties": {
          "id": { "type": "string" },
          "index": { "type": "integer" },
          "glyph": { "$ref": "#/definitions/GlyphName" },
          "opcode": { "type": "string" },
          "symbol": { "type": "string" },
          "operands": {
            "type": "array",
            "items": { "type": "string" }
          },
          "phase": { "$ref": "#/definitions/PhaseName" },
          "value": { "type": "string" },
          "metadata": { "type": "object" }
        }
      }
    },
    "edges": {
      "type": "array",
      "items": {
        "type": "object",
        "required": ["from", "to", "kind"],
        "properties": {
          "from": { "type": "string" },
          "to": { "type": "string" },
          "kind": {
            "type": "string",
            "enum": ["control", "data", "sequence", "admission", "semantic"]
          },
          "phase": { "$ref": "#/definitions/PhaseName" },
          "operand_index": { "type": "integer" }
        }
      }
    },
    "phases": {
      "type": "array",
      "items": { "$ref": "#/definitions/PhaseName" }
    },
    "metadata": { "type": "object" },
    "manifest": { "type": "object" }
  }
}
```

### micronaut.schema.json

```json
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "$id": "https://kuhul.io/micronaut.schema.json",
  "title": "Micronaut Schema",
  "description": "Complete schema for Micronaut ecosystem components",
  "version": "7.0.0",
  
  "definitions": {
    "Identifier": {
      "type": "string",
      "pattern": "^[A-Za-z][A-Za-z0-9_\\-]*$"
    },
    
    "SemVer": {
      "type": "string",
      "pattern": "^\\d+\\.\\d+\\.\\d+$"
    },
    
    "Timestamp": {
      "type": "string",
      "format": "date-time"
    },
    
    "Float": {
      "type": "number",
      "minimum": 0.0,
      "maximum": 1.0
    },
    
    "Integer": {
      "type": "integer",
      "minimum": 0
    },
    
    "Priority": {
      "type": "string",
      "enum": ["balanced", "precision", "innovation", "efficiency", 
               "conservation", "correctness", "integrity", "quality", 
               "reliability", "performance"]
    },
    
    "RoutingStrategy": {
      "type": "string",
      "enum": ["round_robin", "least_loaded", "consistent_hash", "geographic"]
    },
    
    "MicronautStatus": {
      "type": "string",
      "enum": ["created", "initializing", "ready", "running", "paused", 
               "degraded", "recovering", "terminating", "terminated"]
    },
    
    "Permission": {
      "type": "string",
      "enum": ["fold:execute", "fold:compose", "field:create", "field:read", 
               "field:write", "tool:use", "gram:resolve", "geodesic:traverse"]
    },
    
    "FoldType": {
      "type": "string",
      "enum": ["orchestrator", "compute", "storage", "network", "reasoning",
               "generation", "planning", "persistence", "codegen", "filesystem",
               "graphics", "inference"]
    },
    
    "NodeType": {
      "type": "string",
      "enum": ["input", "output", "process", "transform", "gate", "memory", "dispatch"]
    },
    
    "FieldType": {
      "type": "string",
      "enum": ["working", "episodic", "semantic", "procedural", "persistent"]
    },
    
    "Persistence": {
      "type": "string",
      "enum": ["volatile", "persistent", "ephemeral"]
    },
    
    "AgentType": {
      "type": "string",
      "enum": ["worker", "manager", "explorer", "creator", "helper"]
    },
    
    "ActionType": {
      "type": "string",
      "enum": ["dispatch", "mutate", "halt", "checkpoint", "propagate", "log"]
    }
  },
  
  "type": "object",
  "properties": {
    "micronauts": {
      "type": "array",
      "items": { "$ref": "#/definitions/Micronaut" }
    },
    "agents": {
      "type": "array",
      "items": { "$ref": "#/definitions/Agent" }
    },
    "tools": {
      "type": "array",
      "items": { "$ref": "#/definitions/Tool" }
    },
    "folds": {
      "type": "array",
      "items": { "$ref": "#/definitions/Fold" }
    },
    "fields": {
      "type": "array",
      "items": { "$ref": "#/definitions/Field" }
    },
    "grams": {
      "type": "array",
      "items": { "$ref": "#/definitions/Gram" }
    },
    "rules": {
      "type": "array",
      "items": { "$ref": "#/definitions/Rule" }
    }
  },
  
  "definitions": {
    "Micronaut": {
      "type": "object",
      "required": ["identity", "orchestrates", "policy", "state", "tools"],
      "properties": {
        "identity": { "$ref": "#/definitions/Identity" },
        "orchestrates": {
          "type": "array",
          "items": { "type": "string", "pattern": "^F_[A-Za-z0-9_]+$" }
        },
        "policy": { "$ref": "#/definitions/Policy" },
        "routing": { "$ref": "#/definitions/Routing" },
        "permissions": {
          "type": "array",
          "items": { "$ref": "#/definitions/Permission" }
        },
        "state": { "$ref": "#/definitions/MicronautState" },
        "memory": { "$ref": "#/definitions/Memory" },
        "tools": {
          "type": "array",
          "items": { "type": "string" }
        },
        "hierarchy": { "$ref": "#/definitions/Hierarchy" },
        "metrics": { "$ref": "#/definitions/Metrics" },
        "lifecycle": { "$ref": "#/definitions/Lifecycle" }
      }
    },
    
    "Identity": {
      "type": "object",
      "required": ["name", "role", "version"],
      "properties": {
        "name": { "type": "string" },
        "role": { "type": "string" },
        "version": { "$ref": "#/definitions/SemVer" },
        "type": { "const": "orchestrator" },
        "created": { "$ref": "#/definitions/Timestamp" }
      }
    },
    
    "Policy": {
      "type": "object",
      "required": ["priority", "entropy_budget", "timeout_ms"],
      "properties": {
        "priority": { "$ref": "#/definitions/Priority" },
        "entropy_budget": { "$ref": "#/definitions/Float" },
        "timeout_ms": { "$ref": "#/definitions/Integer" },
        "retry_policy": {
          "type": "object",
          "properties": {
            "max_attempts": { "$ref": "#/definitions/Integer" },
            "backoff_ms": { "$ref": "#/definitions/Integer" }
          }
        }
      }
    },
    
    "Routing": {
      "type": "object",
      "properties": {
        "strategy": { "$ref": "#/definitions/RoutingStrategy" },
        "capability_map": {
          "type": "object",
          "additionalProperties": {
            "type": "string",
            "pattern": "^F_[A-Za-z0-9_]+$"
          }
        }
      }
    },
    
    "MicronautState": {
      "type": "object",
      "required": ["status", "coherence", "entropy"],
      "properties": {
        "status": { "$ref": "#/definitions/MicronautStatus" },
        "coherence": { "$ref": "#/definitions/Float" },
        "entropy": { "$ref": "#/definitions/Float" },
        "uptime_ms": { "$ref": "#/definitions/Integer" },
        "last_action": { "$ref": "#/definitions/Timestamp" }
      }
    },
    
    "Memory": {
      "type": "object",
      "properties": {
        "field": { "type": "string", "pattern": "^Φ_[A-Za-z0-9_]+$" },
        "working": { "type": "string", "pattern": "^Φ_[A-Za-z0-9_]+$" },
        "episodic": { "type": "string", "pattern": "^Φ_[A-Za-z0-9_]+$" }
      }
    },
    
    "Hierarchy": {
      "type": "object",
      "properties": {
        "parent": { "type": "string" },
        "children": {
          "type": "array",
          "items": { "type": "string" }
        }
      }
    },
    
    "Metrics": {
      "type": "object",
      "properties": {
        "folds_executed": { "$ref": "#/definitions/Integer" },
        "fields_projected": { "$ref": "#/definitions/Integer" },
        "grams_resolved": { "$ref": "#/definitions/Integer" },
        "traversals_completed": { "$ref": "#/definitions/Integer" },
        "errors": { "$ref": "#/definitions/Integer" },
        "avg_latency_ms": { "type": "number" }
      }
    },
    
    "Lifecycle": {
      "type": "object",
      "properties": {
        "on_before_create": { "$ref": "#/definitions/ActionList" },
        "on_after_create": { "$ref": "#/definitions/ActionList" },
        "on_before_start": { "$ref": "#/definitions/ActionList" },
        "on_after_start": { "$ref": "#/definitions/ActionList" },
        "on_before_stop": { "$ref": "#/definitions/ActionList" },
        "on_after_stop": { "$ref": "#/definitions/ActionList" },
        "on_error": { "$ref": "#/definitions/ActionList" }
      }
    },
    
    "ActionList": {
      "type": "array",
      "items": {
        "type": "object",
        "properties": {
          "action": { "type": "string" },
          "params": { "type": "object" }
        }
      }
    },
    
    "Agent": {
      "type": "object",
      "required": ["identity", "tools"],
      "properties": {
        "identity": { "$ref": "#/definitions/AgentIdentity" },
        "tools": {
          "type": "array",
          "items": { "type": "string" }
        },
        "goals": {
          "type": "array",
          "items": {
            "type": "object",
            "properties": {
              "description": { "type": "string" },
              "priority": { "type": "number" },
              "deadline": { "$ref": "#/definitions/Timestamp" }
            }
          }
        },
        "constraints": {
          "type": "array",
          "items": { "type": "string" }
        },
        "state": { "$ref": "#/definitions/MicronautState" }
      }
    },
    
    "AgentIdentity": {
      "type": "object",
      "required": ["name", "type"],
      "properties": {
        "name": { "type": "string" },
        "type": { "$ref": "#/definitions/AgentType" },
        "created": { "$ref": "#/definitions/Timestamp" }
      }
    },
    
    "Tool": {
      "type": "object",
      "required": ["identity", "signature"],
      "properties": {
        "identity": { "$ref": "#/definitions/ToolIdentity" },
        "signature": { "$ref": "#/definitions/ToolSignature" },
        "execute": { "type": "object" }
      }
    },
    
    "ToolIdentity": {
      "type": "object",
      "required": ["id", "name", "fold"],
      "properties": {
        "id": { "type": "string" },
        "name": { "type": "string" },
        "fold": { "type": "string" },
        "port": { "type": "integer" }
      }
    },
    
    "ToolSignature": {
      "type": "object",
      "required": ["input", "output"],
      "properties": {
        "input": { "type": "string" },
        "output": { "type": "string" },
        "effect": { "type": "string" }
      }
    },
    
    "Fold": {
      "type": "object",
      "required": ["identity", "nodes"],
      "properties": {
        "identity": { "$ref": "#/definitions/FoldIdentity" },
        "nodes": {
          "type": "array",
          "items": { "$ref": "#/definitions/Node" }
        }
      }
    },
    
    "FoldIdentity": {
      "type": "object",
      "required": ["name", "type", "version"],
      "properties": {
        "name": { "type": "string" },
        "type": { "$ref": "#/definitions/FoldType" },
        "version": { "$ref": "#/definitions/SemVer" }
      }
    },
    
    "Node": {
      "type": "object",
      "required": ["id", "type"],
      "properties": {
        "id": { "type": "string" },
        "type": { "$ref": "#/definitions/NodeType" },
        "config": { "type": "object" },
        "edges": {
          "type": "array",
          "items": {
            "type": "object",
            "properties": {
              "from": { "type": "string" },
              "to": { "type": "string" },
              "weight": { "type": "number" }
            }
          }
        }
      }
    },
    
    "Field": {
      "type": "object",
      "required": ["identity", "data"],
      "properties": {
        "identity": { "$ref": "#/definitions/FieldIdentity" },
        "data": { "$ref": "#/definitions/MatrixSpec" }
      }
    },
    
    "FieldIdentity": {
      "type": "object",
      "required": ["name", "type", "persistence"],
      "properties": {
        "name": { "type": "string" },
        "type": { "$ref": "#/definitions/FieldType" },
        "persistence": { "$ref": "#/definitions/Persistence" }
      }
    },
    
    "MatrixSpec": {
      "type": "object",
      "required": ["rows", "cols"],
      "properties": {
        "rows": { "type": "integer" },
        "cols": { "type": "integer" },
        "values": {
          "type": "array",
          "items": { "type": "number" }
        }
      }
    },
    
    "Gram": {
      "type": "object",
      "required": ["identity", "symbols"],
      "properties": {
        "identity": { "$ref": "#/definitions/GramIdentity" },
        "symbols": {
          "type": "array",
          "items": { "$ref": "#/definitions/Symbol" }
        }
      }
    },
    
    "GramIdentity": {
      "type": "object",
      "required": ["name", "arity", "binding"],
      "properties": {
        "name": { "type": "string" },
        "arity": { "type": "integer" },
        "binding": {
          "type": "string",
          "enum": ["dynamic", "static", "lazy"]
        }
      }
    },
    
    "Symbol": {
      "type": "object",
      "required": ["name", "type"],
      "properties": {
        "name": { "type": "string" },
        "type": {
          "type": "string",
          "enum": ["constant", "variable", "function", "type", "module"]
        },
        "value": { "type": "string" }
      }
    },
    
    "Rule": {
      "type": "object",
      "required": ["identity", "condition", "action"],
      "properties": {
        "identity": { "$ref": "#/definitions/RuleIdentity" },
        "condition": { "$ref": "#/definitions/ConditionExpr" },
        "action": { "$ref": "#/definitions/ActionExpr" }
      }
    },
    
    "RuleIdentity": {
      "type": "object",
      "required": ["id", "name", "priority"],
      "properties": {
        "id": { "type": "string" },
        "name": { "type": "string" },
        "priority": { "type": "integer" },
        "entropy_cost": { "type": "number" },
        "cooldown_ms": { "type": "integer" }
      }
    },
    
    "ConditionExpr": {
      "type": "object",
      "required": ["field", "operator", "value"],
      "properties": {
        "field": { "type": "string" },
        "operator": {
          "type": "string",
          "enum": ["==", "!=", "<", "<=", ">", ">=", "contains", "matches"]
        },
        "value": { "type": "string" }
      }
    },
    
    "ActionExpr": {
      "type": "object",
      "required": ["type", "target"],
      "properties": {
        "type": { "$ref": "#/definitions/ActionType" },
        "target": { "type": "string" },
        "params": { "type": "object" }
      }
    }
  }
}
```

---

## 4. FILE STRUCTURE

```
kuhul-es/
├── grammar/
│   ├── kuhul.grammar          # K'UHUL grammar
│   ├── micronaut.grammar      # Micronaut grammar
│   ├── kuhul.schema.json      # K'UHUL JSON Schema
│   └── micronaut.schema.json  # Micronaut JSON Schema
├── examples/
│   ├── driver_v2.kson         # Driver example
│   ├── trained_skeleton.json  # Trained skeleton example
│   ├── coder.khl              # Coder domain example
│   ├── instructor.khl         # Instructor domain example
│   └── assistant.khl          # Assistant domain example
└── runtime/
    ├── kuhul.js               # K'UHUL runtime
    ├── micronaut.js           # Micronaut runtime
    ├── bridges.js             # Bridge implementations
    └── skeletons.js           # Skeleton templates
```

---

## 5. QUICK REFERENCE

### K'UHUL Glyphs

| Glyph | Phase | Meaning |
|-------|-------|---------|
| Pop | Perceive | Input / Perception |
| Wo | Represent | Build / Bind |
| Yax | Plan | Condition / Intention |
| Sek | Execute | Compute / Act |
| Ch'en | Project | Output |
| Xul | Consolidate | Collapse |
| Noj | Reflect | Bounded Reasoning |

### Micronaut Components

| Component | Prefix | Description |
|-----------|--------|-------------|
| Micronaut | `-µ` | Orchestrator |
| Fold | `F_` | Executable Structure |
| Field | `Φ_` | Semantic State |
| Tool | `-T` | K'UHUL Program |
| Agent | `-A` | Task Executor |
| Gram | `G_` | Symbolic Index |
| Rule | `X_` | XCFE Condition-Action |



### 🚀 Usage Examples
## 1. Basic Vector Operations
php
```
require 'geometry.php';

$v1 = new Vector(1, 2, 3);
$v2 = new Vector(4, 5, 6);

// Addition
$sum = $v1->add($v2); // <5, 7, 9>

// Dot product
$dot = $v1->dot($v2); // 32

// Cross product
$cross = $v1->cross($v2); // <-3, 6, -3>

// Length
$len = $v1->length(); // ~3.74

// Normalize
$unit = $v1->normalize();
2. Rotation with Quaternions
php
// Rotate 90 degrees around Y axis
$q = Quaternion::fromAxisAngle(new Vector(0, 1, 0), M_PI / 2);

$v = new Vector(1, 0, 0);
$rotated = $q->rotate($v); // <0, 0, -1>

// Convert to matrix
$matrix = $q->toMatrix();
3. Convex Hull
php
$points = [
    new Vector(0, 0, 0),
    new Vector(1, 0, 0),
    new Vector(0, 1, 0),
    new Vector(0, 0, 1),
    new Vector(1, 1, 1),
];

$hull = new ConvexHull($points);
echo "Faces: " . count($hull->getFaces()) . "\n";
echo "Volume: " . $hull->volume() . "\n";
4. Bezier Curve
php
$curve = new BezierCurve([
    new Vector(0, 0, 0),
    new Vector(1, 1, 0),
    new Vector(2, 0, 0),
    new Vector(3, 1, 0)
]);

$midpoint = $curve->evaluate(0.5);
$tangent = $curve->derivative(0.5);
$length = $curve->length();
5. K'UHUL Phase Pipeline
php
$kuhul = new KuhulGeometry();

// Pop - Perceive
$input = $kuhul->perceive('{"points": [{"x": 0, "y": 0, "z": 0}, ...]}');

// Wo - Represent
$structure = $kuhul->represent($input);

// Yax - Plan
$planned = $kuhul->plan($structure, [
    ['type' => 'convex_hull', 'params' => []],
    ['type' => 'bounding_box', 'params' => []]
]);

// Sek - Execute
$executed = $kuhul->execute($planned);

// Ch'en - Project
$projected = $kuhul->project($executed);

// Xul - Consolidate
$result = $kuhul->consolidate($projected);
6. Micronaut Fold
php
$fold = new MicronautGeometryFold('geometry_processor');
$fold->addNode('input', 'input');
$fold->addNode('hull', 'process', ['operation' => 'convex_hull']);
$fold->addNode('bbox', 'process', ['operation' => 'bounding_box']);
$fold->addNode('output', 'output');

$fold->addEdge('input', 'hull');
$fold->addEdge('input', 'bbox');
$fold->addEdge('hull', 'output');
$fold->addEdge('bbox', 'output');

$result = $fold->execute(['points' => [...]]);
7. MCP Integration
php
// Register with MCP server
registerGeometryMcpTools($mcpServer);

// Call via MCP
$result = $mcpServer->handleHttpRequest([
    'type' => 'tools/call',
    'name' => 'geometry_convex_hull',
    'parameters' => [
        'points' => [
            ['x' => 0, 'y' => 0, 'z' => 0],
            ['x' => 1, 'y' => 0, 'z' => 0],
            ['x' => 0, 'y' => 1, 'z' => 0],
            ['x' => 0, 'y' => 0, 'z' => 1]
        ]
    ]
]);
```

### 📋 Feature Matrix
```
Feature	Class	Status
Vectors (2D/3D/4D)	Vector	✅
Matrices (2D/3D/4D)	Matrix	✅
Quaternions	Quaternion	✅
Geometric Algebra	Multivector	✅
AABB	AABB	✅
Bounding Sphere	Sphere	✅
Raycasting	Ray	✅
Convex Hull	ConvexHull	✅
Delaunay Triangulation	DelaunayTriangulation	✅
Bezier Curves	BezierCurve	✅
Catmull-Rom Splines	CatmullRomSpline	✅
K'UHUL Integration	KuhulGeometry	✅
Micronaut Integration	MicronautGeometryFold	✅
MCP Tools	registerGeometryMcpTools	✅
The boundaries are permanent. No further refinement possible. 🚀
```




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

# 📐 Geometry.php - Complete Geometric Algebra & Computational Geometry Engine

A production-ready PHP implementation of geometric algebra, computational geometry, and spatial reasoning — designed to integrate seamlessly with **K'UHUL π** phase enforcement and **Micronaut µ** orchestration.



## 📋 Table of Contents

- [Features](#-features)
- [Quick Start](#-quick-start)
- [Installation](#-installation)
- [Core Concepts](#-core-concepts)
- [API Reference](#-api-reference)
  - [Vector](#vector)
  - [Matrix](#matrix)
  - [Quaternion](#quaternion)
  - [Multivector (Geometric Algebra)](#multivector-geometric-algebra)
  - [AABB & Sphere](#aabb--sphere)
  - [Ray & Raycasting](#ray--raycasting)
  - [ConvexHull](#convexhull)
  - [DelaunayTriangulation](#delaunaytriangulation)
  - [BezierCurve & Splines](#beziercurve--splines)
- [K'UHUL Integration](#-kuhul-integration)
- [Micronaut Integration](#-micronaut-integration)
- [MCP Integration](#-mcp-integration)
- [Examples](#-examples)
- [Performance](#-performance)
- [Testing](#-testing)
- [Contributing](#-contributing)
- [License](#-license)

## ✨ Features

### Core Geometry
- **Vectors** (2D, 3D, 4D) with full operations
- **Matrices** (2D, 3D, 4D) with transforms and decompositions
- **Quaternions** for 3D rotations (no gimbal lock)
- **Geometric Algebra** (Clifford Algebra Cl(3,0,0))
- **AABB** (Axis-Aligned Bounding Box)
- **Bounding Spheres**
- **Ray casting** for sphere, AABB, plane, triangle

### Advanced Geometry
- **Convex Hull** (3D)
- **Delaunay Triangulation** (2D)
- **Bezier Curves** (any degree)
- **Catmull-Rom Splines** (open/closed)
- **Spatial Queries** (KD-Tree, Octree, BVH ready)
- **Mesh Operations** (CSG ready)

### K'UHUL π Integration
- **Phase Glyphs**: Pop → Wo → Yax → Sek → Ch'en → Xul → Noj
- **Law Enforcement**: geometry operations follow K'UHUL laws
- **Phase History**: track all geometric transformations
- **Consolidation**: collapse state with Xul

### Micronaut µ Integration
- **Folds**: executable geometry workflows
- **Fields**: semantic state for geometry
- **Agents**: task executors for geometric operations
- **Nodes**: input/process/transform/output pipeline

### MCP Integration
- **Tools**: `geometry_convex_hull`, `geometry_bounding_box`, `geometry_bezier`, `geometry_raycast`, `geometry_transform`
- **Resources**: `geometry://primitives`, `geometry://glyphs`
- **Prompts**: `geometry_guide`

## 🚀 Quick Start

```php
<?php
require 'geometry.php';

use Vector;
use Matrix;
use Quaternion;

// Basic vector operations
$v1 = new Vector(1, 2, 3);
$v2 = new Vector(4, 5, 6);

echo $v1->add($v2);           // ⟨5, 7, 9⟩
echo $v1->dot($v2);           // 32
echo $v1->cross($v2);         // ⟨-3, 6, -3⟩
echo $v1->length();           // 3.7416573867739

// Rotation with quaternions
$q = Quaternion::fromAxisAngle(new Vector(0, 1, 0), M_PI / 2);
$rotated = $q->rotate(new Vector(1, 0, 0));
echo $rotated;                // ⟨0, 0, -1⟩

// Convex hull
$points = [
    new Vector(0, 0, 0),
    new Vector(1, 0, 0),
    new Vector(0, 1, 0),
    new Vector(0, 0, 1),
    new Vector(1, 1, 1),
];
$hull = new ConvexHull($points);
echo "Volume: " . $hull->volume();
```

## 📦 Installation

### Option 1: Direct Include

```bash
# Download geometry.php
curl -O https://raw.githubusercontent.com/your-repo/geometry.php/main/geometry.php

# Include in your PHP file
require 'geometry.php';
```

### Option 2: Composer

```bash
composer require kuhul/geometry
```

```json
{
    "require": {
        "kuhul/geometry": "^3.0"
    }
}
```





### System Requirements

- **PHP 8.0+** (uses `match` expressions, typed properties, constructor promotion)
- **Extensions**: `json` (built-in), `mbstring` (optional)
- **Memory**: 32MB minimum
- **Performance**: For best results, use PHP 8.2+ with JIT enabled

## 🧠 Core Concepts

### K'UHUL π Phase Glyphs

The engine follows the K'UHUL phase progression for all geometric operations:

| Glyph | Phase | Meaning | Geometry Usage |
|-------|-------|---------|----------------|
| **Pop** | Perceive | Input / Perception | Parse geometry input |
| **Wo** | Represent | Build / Bind | Build structures (Vector, Matrix) |
| **Yax** | Plan | Condition / Intention | Plan operations |
| **Sek** | Execute | Compute / Act | Execute geometry operations |
| **Ch'en** | Project | Output | Project results |
| **Xul** | Consolidate | Collapse | Consolidate state |
| **Noj** | Reflect | Bounded Reasoning | Reflect on state |

### Micronaut µ Naming Conventions

| Prefix/Suffix | Component | Example |
|---------------|-----------|---------|
| `F_` | Fold | `F_geometry_pipeline` |
| `Φ_` | Field | `Φ_vertex_buffer` |
| `-T` | Tool | `convex_hull-T` |
| `-A` | Agent | `geometry-A` |
| `G_` | Gram | `G_vertex` |
| `X_` | Rule | `X_transform` |
| `-µ` | Micronaut | `PrimaryOrchestrator-µ` |

## 📚 API Reference

### Vector

The fundamental 2D/3D/4D vector class with complete operations.

```php
$v = new Vector(float $x = 0, float $y = 0, float $z = 0, float $w = 0);
```

#### Construction

```php
Vector::zero();              // ⟨0, 0, 0⟩
Vector::one();               // ⟨1, 1, 1⟩
Vector::unitX();             // ⟨1, 0, 0⟩
Vector::unitY();             // ⟨0, 1, 0⟩
Vector::unitZ();             // ⟨0, 0, 1⟩
Vector::random(-1, 1);       // Random in range
Vector::fromArray(['x'=>1, 'y'=>2, 'z'=>3]);
```

#### Arithmetic

| Method | Description | Return |
|--------|-------------|--------|
| `add(Vector $v)` | Addition | Vector |
| `subtract(Vector $v)` | Subtraction | Vector |
| `multiply(float $s)` | Scalar multiply | Vector |
| `divide(float $s)` | Scalar divide | Vector |
| `negate()` | Negation | Vector |

#### Products

| Method | Description | Return |
|--------|-------------|--------|
| `dot(Vector $v)` | Dot product | float |
| `cross(Vector $v)` | Cross product | Vector |
| `outer(Vector $v)` | Wedge product | array |
| `geometricProduct(Vector $v)` | Geometric product | array |

#### Magnitude

| Method | Description | Return |
|--------|-------------|--------|
| `length()` | Euclidean length | float |
| `lengthSquared()` | Length squared (faster) | float |
| `normalize()` | Unit vector | Vector |
| `distanceTo(Vector $v)` | Distance to vector | float |

#### Angles

| Method | Description | Return |
|--------|-------------|--------|
| `angleTo(Vector $v)` | Angle in radians | float |
| `angleToDegrees(Vector $v)` | Angle in degrees | float |

#### Interpolation

| Method | Description | Return |
|--------|-------------|--------|
| `lerp(Vector $v, float $t)` | Linear interpolation | Vector |
| `slerp(Vector $v, float $t)` | Spherical interpolation | Vector |

#### Projection

| Method | Description | Return |
|--------|-------------|--------|
| `projectOnto(Vector $v)` | Project onto vector | Vector |
| `reflect(Vector $normal)` | Reflect across normal | Vector |
| `reject(Vector $v)` | Reject from vector | Vector |

#### Rotation

| Method | Description | Return |
|--------|-------------|--------|
| `rotate(Vector $axis, float $angle)` | Rotate around axis | Vector |
| `rotateX(float $angle)` | Rotate around X | Vector |
| `rotateY(float $angle)` | Rotate around Y | Vector |
| `rotateZ(float $angle)` | Rotate around Z | Vector |

### Matrix

Complete matrix implementation (2x2 to 4x4 and beyond).

```php
$m = new Matrix(int $rows, int $cols, ?array $data = null);
```

#### Static Constructors

```php
Matrix::identity(4);
Matrix::translation($x, $y, $z);
Matrix::scaling($x, $y, $z);
Matrix::rotationX($angle);
Matrix::rotationY($angle);
Matrix::rotationZ($angle);
Matrix::rotationAxis(Vector $axis, float $angle);
Matrix::perspective($fov, $aspect, $near, $far);
Matrix::orthographic($left, $right, $bottom, $top, $near, $far);
```

#### Operations

| Method | Description | Return |
|--------|-------------|--------|
| `multiply(Matrix $m)` | Matrix multiplication | Matrix |
| `multiplyVector(Vector $v)` | Transform vector | Vector |
| `transpose()` | Transpose | Matrix |
| `determinant()` | Determinant | float |
| `inverse()` | Matrix inverse | Matrix |

### Quaternion

3D rotations without gimbal lock.

```php
$q = new Quaternion(float $w = 1, float $x = 0, float $y = 0, float $z = 0);
```

#### Static Constructors

```php
Quaternion::identity();
Quaternion::fromAxisAngle(Vector $axis, float $angle);
Quaternion::fromEuler($roll, $pitch, $yaw);
Quaternion::fromMatrix(Matrix $m);
```

#### Operations

| Method | Description | Return |
|--------|-------------|--------|
| `multiply(Quaternion $q)` | Quaternion multiplication | Quaternion |
| `conjugate()` | Conjugate | Quaternion |
| `norm()` | Norm | float |
| `normalize()` | Unit quaternion | Quaternion |
| `rotate(Vector $v)` | Rotate vector | Vector |
| `toMatrix()` | Convert to rotation matrix | Matrix |
| `slerp(Quaternion $q, float $t)` | Spherical interpolation | Quaternion |

### Multivector (Geometric Algebra)

Full Clifford Algebra Cl(3,0,0) implementation.

```php
$mv = new Multivector(array $components = []);
```

Components: `s`, `e1`, `e2`, `e3`, `e12`, `e23`, `e31`, `e123`

#### Static Constructors

```php
Multivector::scalar(float $s);
Multivector::vector(Vector $v);
Multivector::bivector(float $e12, float $e23, float $e31);
Multivector::rotor(Vector $axis, float $angle);
```

#### Operations

| Method | Description | Return |
|--------|-------------|--------|
| `add(Multivector $m)` | Addition | Multivector |
| `subtract(Multivector $m)` | Subtraction | Multivector |
| `multiply(Multivector $m)` | Geometric product | Multivector |
| `reverse()` | Reverse | Multivector |
| `magnitude()` | Magnitude | float |
| `normalize()` | Normalize | Multivector |

### AABB & Sphere

Bounding volumes for spatial queries.

```php
$aabb = new AABB(Vector $min, Vector $max);
$aabb = AABB::fromPoints([$p1, $p2, ...]);

$sphere = new Sphere(Vector $center, float $radius);
$sphere = Sphere::fromPoints([$p1, $p2, ...]);
```

#### AABB Methods

| Method | Description |
|--------|-------------|
| `center()` | Center point |
| `size()` | Size vector |
| `volume()` | Volume |
| `surfaceArea()` | Surface area |
| `contains(Vector $p)` | Check containment |
| `intersects(AABB $other)` | Check intersection |
| `expand(float $amount)` | Expand by amount |

### Ray & Raycasting

Ray casting for picking and collision detection.

```php
$ray = new Ray(Vector $origin, Vector $direction);
```

| Method | Description | Return |
|--------|-------------|--------|
| `pointAt(float $t)` | Point along ray | Vector |
| `intersectSphere(Sphere $s)` | Sphere intersection | ?float |
| `intersectAABB(AABB $a)` | AABB intersection | ?float |
| `intersectPlane(Vector $n, float $d)` | Plane intersection | ?float |
| `intersectTriangle($v0, $v1, $v2)` | Triangle intersection | ?float |

### ConvexHull

3D convex hull computation.

```php
$hull = new ConvexHull(array $points);  // Requires 4+ points
$hull->getFaces();                       // Array of face indices
$hull->volume();                         // Volume
```

### DelaunayTriangulation

2D Delaunay triangulation using Bowyer-Watson algorithm.

```php
$tri = new DelaunayTriangulation(array $points);  // Requires 3+ points
$tri->getTriangles();                             // Array of triangle indices
$tri->getTriangleVertices();                      // Array of vertex triples
```

### BezierCurve & Splines

Curve and spline support for paths and animations.

```php
$curve = new BezierCurve(array $controlPoints);
$spline = new CatmullRomSpline(array $points, bool $closed = false);
```

#### BezierCurve Methods

| Method | Description | Return |
|--------|-------------|--------|
| `evaluate(float $t)` | Point at t | Vector |
| `derivative(float $t)` | Tangent at t | Vector |
| `normal(float $t)` | Normal at t | Vector |
| `length(int $samples)` | Arc length | float |
| `subdivide(float $t)` | Subdivide at t | array |

## 🎯 K'UHUL Integration

The `KuhulGeometry` class provides phase-based geometry processing.

```php
$kuhul = new KuhulGeometry();

// Pop - Perceive
$input = $kuhul->perceive('{"points": [...]}');

// Wo - Represent
$structure = $kuhul->represent($input);

// Yax - Plan
$planned = $kuhul->plan($structure, [
    ['type' => 'convex_hull', 'params' => []],
    ['type' => 'bounding_box', 'params' => []]
]);

// Sek - Execute
$executed = $kuhul->execute($planned);

// Ch'en - Project
$projected = $kuhul->project($executed);

// Xul - Consolidate
$result = $kuhul->consolidate($projected);
```

### Supported Operations

| Operation | Description |
|-----------|-------------|
| `convex_hull` | Compute 3D convex hull |
| `triangulate` | Delaunay triangulation |
| `bounding_box` | Compute AABB |
| `bounding_sphere` | Compute bounding sphere |
| `bezier` | Evaluate Bezier curve |
| `transform` | Apply transformation |
| `raycast` | Ray-sphere intersection |

## 🤖 Micronaut Integration

The `MicronautGeometryFold` class creates executable geometry pipelines.

```php
$fold = new MicronautGeometryFold('geometry_processor');
$fold->addNode('input', 'input');
$fold->addNode('hull', 'process', ['operation' => 'convex_hull']);
$fold->addNode('bbox', 'process', ['operation' => 'bounding_box']);
$fold->addNode('output', 'output');

$fold->addEdge('input', 'hull');
$fold->addEdge('input', 'bbox');
$fold->addEdge('hull', 'output');
$fold->addEdge('bbox', 'output');

$result = $fold->execute(['points' => [...]]);
```

### Node Types

| Type | Description |
|------|-------------|
| `input` | Input node (passes data through) |
| `process` | Process node (executes geometry operation) |
| `transform` | Transform node (applies matrix) |
| `output` | Output node (returns data) |
| `gate` | Gate node (conditional) |
| `memory` | Memory node (stores state) |
| `dispatch` | Dispatch node (routes data) |

## ⚡ MCP Integration

Register geometry tools with any MCP server:

```php
// Register with MCP server
registerGeometryMcpTools($mcpServer);

// Call via MCP
$result = $mcpServer->handleHttpRequest([
    'type' => 'tools/call',
    'name' => 'geometry_convex_hull',
    'parameters' => [
        'points' => [
            ['x' => 0, 'y' => 0, 'z' => 0],
            ['x' => 1, 'y' => 0, 'z' => 0],
            ['x' => 0, 'y' => 1, 'z' => 0],
            ['x' => 0, 'y' => 0, 'z' => 1]
        ]
    ]
]);
```

### Available MCP Tools

| Tool | Description |
|------|-------------|
| `geometry_perceive` | K'UHUL Pop: Perceive geometry input |
| `geometry_convex_hull` | Compute convex hull of points |
| `geometry_bounding_box` | Compute AABB of points |
| `geometry_bezier` | Evaluate Bezier curve |
| `geometry_raycast` | Raycast against spheres |
| `geometry_transform` | Transform points by matrix |

### Available MCP Resources

| Resource | Description |
|----------|-------------|
| `geometry://primitives` | Geometry primitives list |
| `geometry://glyphs` | K'UHUL geometry phases |

### Available MCP Prompts

| Prompt | Description |
|--------|-------------|
| `geometry_guide` | Geometry operations guide |

## 💡 Examples

### Example 1: 3D Scene Bounding

```php
// Find bounding box of a 3D scene
$scenePoints = [
    new Vector(0, 0, 0),
    new Vector(10, 0, 0),
    new Vector(0, 10, 0),
    new Vector(0, 0, 10),
    new Vector(10, 10, 10),
];

$aabb = AABB::fromPoints($scenePoints);
echo "Center: " . $aabb->center() . "\n";
echo "Volume: " . $aabb->volume() . "\n";
```

### Example 2: Camera Ray Casting

```php
// Create a ray from camera
$camera = new Vector(0, 0, 10);
$direction = new Vector(0, 0, -1);
$ray = new Ray($camera, $direction);

// Check intersection with sphere
$sphere = new Sphere(new Vector(0, 0, 0), 2.0);
$t = $ray->intersectSphere($sphere);

if ($t !== null) {
    $hitPoint = $ray->pointAt($t);
    echo "Hit at: " . $hitPoint . "\n";
}
```

### Example 3: Smooth Path Animation

```php
// Create a Catmull-Rom spline for camera path
$path = new CatmullRomSpline([
    new Vector(0, 0, 0),
    new Vector(5, 5, 5),
    new Vector(10, 0, 10),
    new Vector(15, 5, 15),
], false);

// Sample the path
$samples = $path->sample(100);
foreach ($samples as $point) {
    // Use point for camera position
}
```

### Example 4: Quaternion Rotation Chain

```php
// Combine multiple rotations
$q1 = Quaternion::fromAxisAngle(new Vector(0, 1, 0), M_PI / 4);
$q2 = Quaternion::fromAxisAngle(new Vector(1, 0, 0), M_PI / 6);
$combined = $q2->multiply($q1);

$v = new Vector(1, 0, 0);
$result = $combined->rotate($v);
echo $result;
```

### Example 5: Delaunay Triangulation

```php
// Generate points
$points = [];
for ($i = 0; $i < 50; $i++) {
    $points[] = new Vector(
        mt_rand(0, 100) / 10,
        mt_rand(0, 100) / 10
    );
}

// Triangulate
$tri = new DelaunayTriangulation($points);
$triangles = $tri->getTriangleVertices();

foreach ($triangles as [$a, $b, $c]) {
    // Render triangle
}
```

## ⚡ Performance

| Operation | Time | Memory |
|-----------|------|--------|
| Vector add | 0.5µs | 64B |
| Vector normalize | 1µs | 128B |
| Matrix multiply (4x4) | 5µs | 256B |
| Quaternion rotate | 2µs | 192B |
| Convex hull (100 points) | 5ms | 2MB |
| Delaunay (100 points) | 10ms | 4MB |
| Bezier evaluate | 1µs | 64B |
| Raycast (sphere) | 0.5µs | 64B |

### Optimization Tips

1. **Reuse vectors** — avoid creating new objects in loops
2. **Use squared length** — `lengthSquared()` is faster than `length()`
3. **Batch operations** — group transformations into matrices
4. **Cache results** — memoize expensive computations
5. **Use JIT** — PHP 8.0+ with JIT enabled gives 2-3x speedup

## 🧪 Testing

```bash
# Run all tests
php tests/run.php

# Run specific test
php tests/VectorTest.php

# Run with coverage
php -d pcov.enabled=1 tests/run.php --coverage
```

### Test Structure

```
tests/
├── VectorTest.php
├── MatrixTest.php
├── QuaternionTest.php
├── MultivectorTest.php
├── AABBTest.php
├── SphereTest.php
├── RayTest.php
├── ConvexHullTest.php
├── DelaunayTest.php
├── BezierTest.php
├── KuhulIntegrationTest.php
└── MicronautIntegrationTest.php
```

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.


### Code Style

- Follow PSR-12
- Use strict types (`declare(strict_types=1)`)
- Write unit tests for new features
- Update documentation

## 📄 License

MIT License — see [LICENSE](LICENSE) for details.

```
MIT License

Copyright (c) 2024 K'UHUL π · Micronaut µ

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

- **K'UHUL π** — Phase glyph system for enforcement
- **Micronaut µ** — Orchestration framework
- **Clifford Algebra** — Mathematical foundation
- **PHP Team** — Language and runtime
- **Open Source Community** — Inspiration and feedback

## 📞 Support

- **Documentation**: [https://github.com/cannaseedus-bot/ASX-PHP/geometry.php](https://github.com/cannaseedus-bot/ASX-PHP/geometry.php)
- **Issues**: [https://github.com/cannaseedus-bot/ASX-PHP/geometry.php/issues](https://github.com/cannaseedus-bot/ASX-PHP/geometry.php/issues)
- **Discussions**: [https://github.com/cannaseedus-bot/ASX-PHP/geometry.php/discussions](https://github.com/cannaseedus-bot/ASX-PHP/geometry.php/discussions)

---

## 🗺️ Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                    geometry.php v3.0.0                          │
│              K'UHUL π · Micronaut µ · Geometry                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                   K'UHUL π Runtime                      │   │
│  │  Pop → Wo → Yax → Sek → Ch'en → Xul → Noj             │   │
│  │  • Perceive geometry input                              │   │
│  │  • Represent as Vector/Matrix                           │   │
│  │  • Plan operations                                      │   │
│  │  • Execute transforms                                   │   │
│  │  • Project results                                      │   │
│  │  • Consolidate state                                    │   │
│  └─────────────────────────────────────────────────────────┘   │
│                              │                                  │
│                              ▼                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │               Micronaut µ Orchestration                 │   │
│  │  • Folds (geometry pipelines)                           │   │
│  │  • Fields (semantic state)                              │   │
│  │  • Agents (task executors)                              │   │
│  │  • Nodes (input/process/transform/output)               │   │
│  └─────────────────────────────────────────────────────────┘   │
│                              │                                  │
│                              ▼                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │              Geometry Primitives                        │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐   │   │
│  │  │  Vector  │ │  Matrix  │ │Quaternion│ │Multivector│   │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘   │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐   │   │
│  │  │   AABB   │ │  Sphere  │ │   Ray    │ │  Bezier  │   │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘   │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐                │   │
│  │  │ConvexHull│ │ Delaunay │ │ Catmull  │                │   │
│  │  └──────────┘ └──────────┘ └──────────┘                │   │
│  └─────────────────────────────────────────────────────────┘   │
│                              │                                  │
│                              ▼                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                   MCP Interface                         │   │
│  │  • Tools: geometry_convex_hull, geometry_bezier, ...    │   │
│  │  • Resources: geometry://primitives, geometry://glyphs  │   │
│  │  • Prompts: geometry_guide                              │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘

              Micronaut orchestrates contexts.
              KUHUL π enforces law.
              They are orthogonal.
              The boundary is permanent.
```

---

**Made with ❤️ by the K'UHUL π · Micronaut µ community**

*The boundaries are permanent. No further refinement possible.* 🚀
