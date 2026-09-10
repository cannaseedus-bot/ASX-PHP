#!/usr/bin/env php
<?php
// cli.php - Run with: php cli.php

// Include setup
require_once __DIR__ . '/public/index.php';

// Run MCP in stdio mode
$mcp = McpServer::getInstance();
$mcp->handleStdio();