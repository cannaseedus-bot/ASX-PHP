<#
.SYNOPSIS
    PHPMicronaut.ps1 - K'UHUL & Micronaut Supercharged PHP Runtime
.DESCRIPTION
    A complete PHP server with K'UHUL phase glyphs and Micronaut orchestration.
    Features: PHP parsing, K'UHUL law enforcement, Micronaut agents, MCP, 
    DNS caching, file caching, REST API, JSON-RPC, and more!
.PARAMETER Port
    Server port (default: 8081)
.PARAMETER DocumentRoot
    Document root directory (default: ./public)
.PARAMETER CacheDir
    Cache directory (default: ./cache)
.PARAMETER LogDir
    Log directory (default: ./logs)
.PARAMETER EnableMCP
    Enable MCP support (default: $true)
.PARAMETER EnableKuhul
    Enable K'UHUL phase enforcement (default: $true)
.PARAMETER EnableMicronaut
    Enable Micronaut orchestration (default: $true)
.EXAMPLE
    .\PHPMicronaut.ps1 -Port 8081 -EnableKuhul $true -EnableMicronaut $true
.NOTES
    Author: PHP Runtime · K'UHUL π · Micronaut
    Version: 3.0.0
    Protocol: kast/1 · KXML · MCP 2025-03-26
#>

[CmdletBinding()]
param(
    [int]$Port = 8080,
    [string]$DocumentRoot = "./public",
    [string]$CacheDir = "./cache",
    [string]$LogDir = "./logs",
    [bool]$EnableMCP = $true,
    [bool]$EnableKuhul = $true,
    [bool]$EnableMicronaut = $true,
    [bool]$EnableDNS = $true,
    [bool]$EnableFileCache = $true,
    [bool]$EnableJSONRPC = $true,
    [bool]$EnableREST = $true
)

#region Setup & Logging

$ErrorActionPreference = "Stop"
$PSDefaultParameterValues['*:ErrorAction'] = 'Stop'

# Create directories
foreach ($dir in @($DocumentRoot, $CacheDir, $LogDir)) {
    if (!(Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
        Write-Host "⟁ Created directory: $dir ⟁" -ForegroundColor Green
    }
}

$LogFile = Join-Path $LogDir "server.log"
$KuhulLogFile = Join-Path $LogDir "kuhul.log"
$MicronautLogFile = Join-Path $LogDir "micronaut.log"

function Write-Log {
    param([string]$Message, [string]$Level = "INFO", [string]$Domain = "SERVER")
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss.fff"
    $glyph = switch ($Domain) {
        "KUHUL" { "π" }
        "MICRONAUT" { "µ" }
        "MCP" { "⚡" }
        "PHP" { "🐘" }
        default { "◆" }
    }
    $logEntry = "[$timestamp] [$glyph] [$Domain] [$Level] $Message"
    Add-Content -Path $LogFile -Value $logEntry
    
    if ($Domain -eq "KUHUL") {
        Add-Content -Path $KuhulLogFile -Value $logEntry
    }
    if ($Domain -eq "MICRONAUT") {
        Add-Content -Path $MicronautLogFile -Value $logEntry
    }
    
    $color = switch ($Level) {
        "ERROR" { "Red" }
        "WARNING" { "Yellow" }
        "PHASE" { "Magenta" }
        "ORCHESTRATE" { "Cyan" }
        "ENFORCE" { "Green" }
        default { "White" }
    }
    Write-Host $logEntry -ForegroundColor $color
}

Write-Log "⟁ K'UHUL π · Micronaut µ · PHP Runtime v3.0 ⟁" "PHASE" "KUHUL"
Write-Log "⚡ Micronaut orchestrates. K'UHUL enforces. They are orthogonal." "ORCHESTRATE" "MICRONAUT"

#region K'UHUL Core

<#
    K'UHUL π - Law Enforcement Layer
    Phase Glyphs: Pop → Wo → Yax → Sek → Ch'en → Xul → Noj
    Invariant: K'UHUL cannot orchestrate. Micronaut cannot enforce.
#>

class KuhulPhase {
    static [string[]]$GLYPHS = @("Pop", "Wo", "Yax", "Sek", "Ch'en", "Xul", "Noj")
    static [hashtable]$MEANINGS = @{
        "Pop" = "Perceive / Input"
        "Wo" = "Represent / Build / Bind"
        "Yax" = "Plan / Condition / Intention"
        "Sek" = "Execute / Compute / Act"
        "Ch'en" = "Project / Output"
        "Xul" = "Consolidate / Collapse"
        "Noj" = "Reflect / Bounded Reasoning"
    }
    
    [string]$Glyph
    [string]$Value
    [int]$Index
    [hashtable]$Metadata
    
    KuhulPhase([string]$glyph) {
        $idx = [array]::IndexOf([KuhulPhase]::GLYPHS, $glyph)
        if ($idx -eq -1) {
            throw "Invalid K'UHUL glyph: $glyph"
        }
        $this.Glyph = $glyph
        $this.Index = $idx
        $this.Value = [KuhulPhase]::MEANINGS[$glyph]
        $this.Metadata = @{
            phase = $glyph
            index = $idx
            meaning = $this.Value
            timestamp = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ss.fffZ")
        }
    }
    
    [bool]IsBefore([KuhulPhase]$other) {
        return $this.Index -lt $other.Index
    }
    
    [bool]IsAfter([KuhulPhase]$other) {
        return $this.Index -gt $other.Index
    }
    
    [bool]IsValidTransition([KuhulPhase]$to) {
        # Valid transitions: sequential or same phase (consolidation)
        return ($to.Index - $this.Index) -in @(0, 1)
    }
    
    [string]ToString() {
        return "[$($this.Glyph)] $($this.Value)"
    }
}

class KuhulLaw {
    [string]$Name
    [string]$Hash
    [hashtable]$Definition
    [hashtable]$Invariants
    [array]$Phases
    
    KuhulLaw([string]$name, [hashtable]$definition) {
        $this.Name = $name
        $this.Definition = $definition
        $this.Invariants = @{
            "collapse_only" = $true
            "field_perception" = $true
            "compression_law" = $true
            "unreachable_states" = $true
        }
        $this.Phases = @()
        
        # Extract phases from definition
        if ($definition.ContainsKey("phases")) {
            foreach ($g in $definition["phases"]) {
                $this.Phases += [KuhulPhase]::new($g)
            }
        }
        
        # Compute semantic hash
        $json = $definition | ConvertTo-Json -Compress
        $bytes = [System.Text.Encoding]::UTF8.GetBytes($json)
        $hashBytes = [System.Security.Cryptography.SHA256]::Create().ComputeHash($bytes)
        $this.Hash = [System.BitConverter]::ToString($hashBytes).Replace("-", "").ToLower()
    }
    
    [bool]Enforce([hashtable]$state) {
        Write-Log "π ENFORCE: $($this.Name) | Hash: $($this.Hash.Substring(0,8))" "ENFORCE" "KUHUL"
        
        # Check each invariant
        foreach ($key in $this.Invariants.Keys) {
            if (-not $this.ValidateInvariant($key, $state)) {
                Write-Log "π REJECT: Invariant '$key' violated" "ERROR" "KUHUL"
                return $false
            }
        }
        
        # Validate phase progression
        if ($this.Phases.Count -gt 0) {
            $result = $this.ValidatePhaseProgression($state)
            if (-not $result) {
                Write-Log "π REJECT: Invalid phase progression" "ERROR" "KUHUL"
                return $false
            }
        }
        
        Write-Log "π COLLAPSE: Law enforced successfully" "ENFORCE" "KUHUL"
        return $true
    }
    
    [bool]ValidateInvariant([string]$name, [hashtable]$state) {
        switch ($name) {
            "collapse_only" {
                # Only Xul can collapse
                if ($state.ContainsKey("phase") -and $state["phase"] -ne "Xul") {
                    return $false
                }
                return $true
            }
            "field_perception" {
                # Must have a field to perceive
                return $state.ContainsKey("field")
            }
            "compression_law" {
                # State must be compressible
                try {
                    $json = $state | ConvertTo-Json -Compress
                    return $true
                } catch {
                    return $false
                }
            }
            default {
                return $true
            }
        }
    }
    
    [bool]ValidatePhaseProgression([hashtable]$state) {
        $currentPhase = $state["phase"] ?? "Pop"
        $currentIdx = [array]::IndexOf([KuhulPhase]::GLYPHS, $currentPhase)
        
        # Find the last phase in our law
        $lastPhase = $this.Phases[-1]
        $lastIdx = $lastPhase.Index
        
        # Can't go beyond the law's defined phases
        if ($currentIdx -gt $lastIdx) {
            return $false
        }
        
        return $true
    }
}

class KuhulRuntime {
    [hashtable]$Laws
    [hashtable]$State
    [array]$PhaseHistory
    [KuhulPhase]$CurrentPhase
    [bool]$IsEnforcing
    
    KuhulRuntime() {
        $this.Laws = @{}
        $this.State = @{
            fields = @{}
            phase = "Pop"
            entropy = 0.0
            coherence = 1.0
        }
        $this.PhaseHistory = @()
        $this.CurrentPhase = [KuhulPhase]::new("Pop")
        $this.IsEnforcing = $false
        Write-Log "π K'UHUL runtime initialized" "PHASE" "KUHUL"
    }
    
    [void]DefineLaw([string]$name, [hashtable]$definition) {
        $law = [KuhulLaw]::new($name, $definition)
        $this.Laws[$name] = $law
        Write-Log "π DEFINED: $name | Hash: $($law.Hash.Substring(0,8))" "ENFORCE" "KUHUL"
    }
    
    [bool]EnforceLaw([string]$name, [hashtable]$state = $null) {
        if (-not $this.Laws.ContainsKey($name)) {
            Write-Log "π ERROR: Law '$name' not found" "ERROR" "KUHUL"
            return $false
        }
        
        $this.IsEnforcing = $true
        $law = $this.Laws[$name]
        
        # Merge state
        if ($state) {
            foreach ($key in $state.Keys) {
                $this.State[$key] = $state[$key]
            }
        }
        
        # Enforce the law
        $result = $law.Enforce($this.State)
        
        $this.IsEnforcing = $false
        return $result
    }
    
    [void]TransitionPhase([string]$toGlyph) {
        $toPhase = [KuhulPhase]::new($toGlyph)
        
        if (-not $this.CurrentPhase.IsValidTransition($toPhase)) {
            Write-Log "π REJECT: Invalid phase transition $($this.CurrentPhase.Glyph) → $toGlyph" "ERROR" "KUHUL"
            throw "Invalid K'UHUL phase transition"
        }
        
        $this.PhaseHistory += $this.CurrentPhase
        $this.CurrentPhase = $toPhase
        $this.State["phase"] = $toGlyph
        
        Write-Log "π PHASE: $($this.CurrentPhase.ToString())" "PHASE" "KUHUL"
    }
    
    [hashtable]Perceive([hashtable]$input) {
        $this.TransitionPhase("Pop")
        $this.State["perception"] = $input
        Write-Log "π Pop: Perceived input" "PHASE" "KUHUL"
        return $this.State
    }
    
    [hashtable]Represent([string]$symbol, $value) {
        $this.TransitionPhase("Wo")
        $this.State["fields"][$symbol] = $value
        Write-Log "π Wo: Bound $symbol = $value" "PHASE" "KUHUL"
        return $this.State
    }
    
    [hashtable]Plan([string]$intention) {
        $this.TransitionPhase("Yax")
        $this.State["intention"] = $intention
        Write-Log "π Yax: Planned $intention" "PHASE" "KUHUL"
        return $this.State
    }
    
    [hashtable]Execute([scriptblock]$action) {
        $this.TransitionPhase("Sek")
        $result = & $action $this.State
        $this.State["result"] = $result
        Write-Log "π Sek: Executed action" "PHASE" "KUHUL"
        return $this.State
    }
    
    [hashtable]Project([string]$target) {
        $this.TransitionPhase("Ch'en")
        $output = $this.State[$target] ?? $this.State["result"]
        $this.State["output"] = $output
        Write-Log "π Ch'en: Projected $target" "PHASE" "KUHUL"
        return $this.State
    }
    
    [hashtable]Consolidate() {
        $this.TransitionPhase("Xul")
        $this.State["consolidated"] = $true
        $this.State["timestamp"] = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ss.fffZ")
        Write-Log "π Xul: Consolidated" "PHASE" "KUHUL"
        return $this.State
    }
    
    [hashtable]Reflect() {
        $this.TransitionPhase("Noj")
        $reflection = @{
            phases = $this.PhaseHistory.Count
            current_phase = $this.CurrentPhase.Glyph
            entropy = $this.State["entropy"]
            coherence = $this.State["coherence"]
            fields = $this.State["fields"].Count
            laws = $this.Laws.Count
            enforcing = $this.IsEnforcing
        }
        $this.State["reflection"] = $reflection
        Write-Log "π Noj: Reflected on state" "PHASE" "KUHUL"
        return $this.State
    }
    
    [hashtable]GetState() {
        return $this.State
    }
    
    [array]GetPhaseHistory() {
        return $this.PhaseHistory
    }
}

#region Micronaut Core

<#
    Micronaut µ - Orchestration Layer
    Context: K'UHUL cannot orchestrate. Micronaut cannot enforce.
    They are orthogonal. The boundary is permanent.
#>

class MicronautIdentity {
    [string]$Name
    [string]$Role
    [string]$Version
    [string]$Created
    [string]$Type
    
    MicronautIdentity([string]$name, [string]$role, [string]$version = "1.0.0") {
        $this.Name = $name
        $this.Role = $role
        $this.Version = $version
        $this.Created = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ss.fffZ")
        $this.Type = "orchestrator"
    }
    
    [hashtable]ToHash() {
        return @{
            name = $this.Name
            role = $this.Role
            version = $this.Version
            created = $this.Created
            type = $this.Type
        }
    }
}

class MicronautPolicy {
    [string]$Priority
    [float]$EntropyBudget
    [int]$TimeoutMs
    [hashtable]$RetryPolicy
    
    MicronautPolicy([string]$priority = "balanced", [float]$entropyBudget = 0.5, [int]$timeoutMs = 5000) {
        $this.Priority = $priority
        $this.EntropyBudget = $entropyBudget
        $this.TimeoutMs = $timeoutMs
        $this.RetryPolicy = @{
            max_attempts = 3
            backoff_ms = 1000
        }
    }
    
    [hashtable]ToHash() {
        return @{
            priority = $this.Priority
            entropy_budget = $this.EntropyBudget
            timeout_ms = $this.TimeoutMs
            retry_policy = $this.RetryPolicy
        }
    }
}

class MicronautState {
    [string]$Status
    [float]$Coherence
    [float]$Entropy
    [int]$UptimeMs
    [string]$LastAction
    
    MicronautState() {
        $this.Status = "created"
        $this.Coherence = 1.0
        $this.Entropy = 0.0
        $this.UptimeMs = 0
        $this.LastAction = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ss.fffZ")
    }
    
    [void]Transition([string]$newStatus) {
        $validStatuses = @("created", "initializing", "ready", "running", "paused", "degraded", "recovering", "terminating", "terminated")
        if ($newStatus -notin $validStatuses) {
            throw "Invalid status: $newStatus"
        }
        $this.Status = $newStatus
        $this.LastAction = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ss.fffZ")
        Write-Log "µ STATUS: $newStatus" "ORCHESTRATE" "MICRONAUT"
    }
    
    [hashtable]ToHash() {
        return @{
            status = $this.Status
            coherence = $this.Coherence
            entropy = $this.Entropy
            uptime_ms = $this.UptimeMs
            last_action = $this.LastAction
        }
    }
}

class MicronautAgent {
    [MicronautIdentity]$Identity
    [string]$Type
    [array]$Tools
    [array]$Goals
    [array]$Constraints
    [MicronautState]$State
    [hashtable]$Metrics
    
    MicronautAgent([string]$name, [string]$type = "worker") {
        $this.Identity = [MicronautIdentity]::new($name, "agent", "1.0.0")
        $this.Type = $type
        $this.Tools = @()
        $this.Goals = @()
        $this.Constraints = @()
        $this.State = [MicronautState]::new()
        $this.Metrics = @{
            tasks_completed = 0
            tasks_failed = 0
            avg_latency_ms = 0
        }
        $this.State.Transition("ready")
        Write-Log "µ Agent $name ($type) created" "ORCHESTRATE" "MICRONAUT"
    }
    
    [void]AddTool([string]$toolName) {
        $this.Tools += $toolName
        Write-Log "µ Tool added: $toolName" "ORCHESTRATE" "MICRONAUT"
    }
    
    [void]AddGoal([string]$description, [float]$priority = 1.0, [string]$deadline = $null) {
        $goal = @{
            description = $description
            priority = $priority
            deadline = $deadline ?? (Get-Date).AddDays(7).ToString("yyyy-MM-ddTHH:mm:ss.fffZ")
        }
        $this.Goals += $goal
        Write-Log "µ Goal: $description" "ORCHESTRATE" "MICRONAUT"
    }
    
    [void]AddConstraint([string]$constraint) {
        $this.Constraints += $constraint
        Write-Log "µ Constraint: $constraint" "ORCHESTRATE" "MICRONAUT"
    }
    
    [hashtable]Execute([string]$toolName, [hashtable]$params) {
        if ($toolName -notin $this.Tools) {
            throw "Tool '$toolName' not available"
        }
        
        $this.State.Transition("running")
        $startTime = Get-Date
        
        try {
            Write-Log "µ Executing tool: $toolName" "ORCHESTRATE" "MICRONAUT"
            
            # Simulate execution (in real implementation, this would call the tool)
            $result = @{
                tool = $toolName
                params = $params
                result = "Success"
                timestamp = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ss.fffZ")
            }
            
            $this.Metrics.tasks_completed++
            $elapsed = ((Get-Date) - $startTime).TotalMilliseconds
            $this.Metrics.avg_latency_ms = ($this.Metrics.avg_latency_ms * ($this.Metrics.tasks_completed - 1) + $elapsed) / $this.Metrics.tasks_completed
            
            $this.State.Transition("ready")
            return $result
            
        } catch {
            $this.Metrics.tasks_failed++
            $this.State.Transition("degraded")
            Write-Log "µ ERROR: $($_.Exception.Message)" "ERROR" "MICRONAUT"
            throw
        }
    }
    
    [hashtable]GetStatus() {
        return @{
            identity = $this.Identity.ToHash()
            type = $this.Type
            tools = $this.Tools
            goals = $this.Goals
            constraints = $this.Constraints
            state = $this.State.ToHash()
            metrics = $this.Metrics
        }
    }
}

class Micronaut {
    [MicronautIdentity]$Identity
    [array]$Orchestrates
    [MicronautPolicy]$Policy
    [hashtable]$Routing
    [array]$Permissions
    [MicronautState]$State
    [hashtable]$Memory
    [array]$Tools
    [hashtable]$Hierarchy
    [hashtable]$Metrics
    [hashtable]$Lifecycle
    [array]$Agents
    [hashtable]$Fields
    [hashtable]$Folds
    
    Micronaut([string]$name, [string]$role = "orchestrator") {
        $this.Identity = [MicronautIdentity]::new($name, $role)
        $this.Orchestrates = @()
        $this.Policy = [MicronautPolicy]::new()
        $this.Routing = @{
            strategy = "round_robin"
            capability_map = @{}
        }
        $this.Permissions = @("fold:execute", "field:read", "tool:use")
        $this.State = [MicronautState]::new()
        $this.Memory = @{
            field = $null
            working = $null
            episodic = $null
        }
        $this.Tools = @()
        $this.Hierarchy = @{
            parent = $null
            children = @()
        }
        $this.Metrics = @{
            folds_executed = 0
            fields_projected = 0
            grams_resolved = 0
            traversals_completed = 0
            errors = 0
            avg_latency_ms = 0
        }
        $this.Lifecycle = @{
            on_before_create = @()
            on_after_create = @()
            on_before_start = @()
            on_after_start = @()
            on_before_stop = @()
            on_after_stop = @()
            on_error = @()
        }
        $this.Agents = @()
        $this.Fields = @{}
        $this.Folds = @{}
        
        $this.State.Transition("initializing")
        Write-Log "µ Micronaut $name created" "ORCHESTRATE" "MICRONAUT"
        $this.State.Transition("ready")
    }
    
    [void]Orchestrate([string]$foldName) {
        if ($foldName -notin $this.Orchestrates) {
            $this.Orchestrates += $foldName
            Write-Log "µ Orchestrating: $foldName" "ORCHESTRATE" "MICRONAUT"
        }
    }
    
    [void]AddTool([string]$toolName) {
        if ($toolName -notin $this.Tools) {
            $this.Tools += $toolName
            Write-Log "µ Tool registered: $toolName" "ORCHESTRATE" "MICRONAUT"
        }
    }
    
    [void]AddAgent([MicronautAgent]$agent) {
        $this.Agents += $agent
        Write-Log "µ Agent registered: $($agent.Identity.Name)" "ORCHESTRATE" "MICRONAUT"
    }
    
    [void]CreateField([string]$name, [string]$type = "working", [string]$persistence = "volatile") {
        $this.Fields[$name] = @{
            identity = @{
                name = $name
                type = $type
                persistence = $persistence
            }
            data = @{
                rows = 0
                cols = 0
                values = @()
            }
            created = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ss.fffZ")
        }
        Write-Log "µ FIELD Φ_$name ($type) created" "ORCHESTRATE" "MICRONAUT"
    }
    
    [void]CreateFold([string]$name, [string]$type = "compute") {
        $this.Folds[$name] = @{
            identity = @{
                name = $name
                type = $type
                version = "1.0.0"
            }
            nodes = @()
            created = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ss.fffZ")
        }
        Write-Log "µ FOLD F_$name ($type) created" "ORCHESTRATE" "MICRONAUT"
    }
    
    [void]AddFoldNode([string]$foldName, [hashtable]$node) {
        if (-not $this.Folds.ContainsKey($foldName)) {
            throw "Fold '$foldName' not found"
        }
        $this.Folds[$foldName].nodes += $node
        Write-Log "µ Fold $foldName node added: $($node.id)" "ORCHESTRATE" "MICRONAUT"
    }
    
    [hashtable]ExecuteFold([string]$foldName, [hashtable]$input) {
        if ($foldName -notin $this.Orchestrates) {
            throw "Fold '$foldName' is not orchestrated by this Micronaut"
        }
        if (-not $this.Folds.ContainsKey($foldName)) {
            throw "Fold '$foldName' not found"
        }
        
        $this.State.Transition("running")
        $startTime = Get-Date
        
        try {
            Write-Log "µ Executing fold: $foldName" "ORCHESTRATE" "MICRONAUT"
            
            # Execute fold nodes (simplified)
            $result = @{
                fold = $foldName
                input = $input
                output = "Executed"
                timestamp = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ss.fffZ")
            }
            
            $this.Metrics.folds_executed++
            $elapsed = ((Get-Date) - $startTime).TotalMilliseconds
            $this.Metrics.avg_latency_ms = ($this.Metrics.avg_latency_ms * ($this.Metrics.folds_executed - 1) + $elapsed) / $this.Metrics.folds_executed
            
            $this.State.Transition("ready")
            return $result
            
        } catch {
            $this.Metrics.errors++
            $this.State.Transition("degraded")
            Write-Log "µ ERROR: $($_.Exception.Message)" "ERROR" "MICRONAUT"
            throw
        }
    }
    
    [hashtable]GetStatus() {
        return @{
            identity = $this.Identity.ToHash()
            orchestrates = $this.Orchestrates
            policy = $this.Policy.ToHash()
            routing = $this.Routing
            permissions = $this.Permissions
            state = $this.State.ToHash()
            memory = $this.Memory
            tools = $this.Tools
            hierarchy = $this.Hierarchy
            metrics = $this.Metrics
            agents = $this.Agents | ForEach-Object { $_.GetStatus() }
            folds = $this.Folds.Keys
            fields = $this.Fields.Keys
        }
    }
}

#region MCP Server (Enhanced)

class McpServerEnhanced {
    [hashtable]$Tools
    [hashtable]$Resources
    [hashtable]$Prompts
    [string]$Transport
    [hashtable]$ServerInfo
    [bool]$Initialized
    [KuhulRuntime]$Kuhul
    [Micronaut]$Micronaut
    
    McpServerEnhanced([KuhulRuntime]$kuhul, [Micronaut]$micronaut) {
        $this.Tools = @{}
        $this.Resources = @{}
        $this.Prompts = @{}
        $this.Transport = "http"
        $this.ServerInfo = @{
            name = "PHPServer.ps1 · K'UHUL π · Micronaut µ"
            version = "3.0.0"
            protocol_version = "2025-03-26"
            capabilities = @{
                kuhul = $true
                micronaut = $true
                kxml = $true
                mcp = $true
            }
        }
        $this.Initialized = $false
        $this.Kuhul = $kuhul
        $this.Micronaut = $micronaut
        $this.RegisterDefaultTools()
        $this.RegisterDefaultResources()
        $this.RegisterDefaultPrompts()
    }
    
    [void]RegisterDefaultTools() {
        # K'UHUL Phase Tools
        $this.Tools["kuhul_perceive"] = @{
            name = "kuhul_perceive"
            description = "K'UHUL Pop: Perceive input"
            handler = {
                param($params)
                $input = $params.input ?? @{}
                return $this.Kuhul.Perceive($input)
            }
            params = @{ input = @{ type = "object"; description = "Input to perceive" } }
        }
        
        $this.Tools["kuhul_represent"] = @{
            name = "kuhul_represent"
            description = "K'UHUL Wo: Represent symbol"
            handler = {
                param($params)
                $symbol = $params.symbol
                $value = $params.value
                return $this.Kuhul.Represent($symbol, $value)
            }
            params = @{
                symbol = @{ type = "string"; description = "Symbol name" }
                value = @{ type = "any"; description = "Value to bind" }
            }
        }
        
        $this.Tools["kuhul_plan"] = @{
            name = "kuhul_plan"
            description = "K'UHUL Yax: Plan intention"
            handler = {
                param($params)
                $intention = $params.intention
                return $this.Kuhul.Plan($intention)
            }
            params = @{ intention = @{ type = "string"; description = "Intention to plan" } }
        }
        
        $this.Tools["kuhul_execute"] = @{
            name = "kuhul_execute"
            description = "K'UHUL Sek: Execute action"
            handler = {
                param($params)
                $action = $params.action
                $script = [scriptblock]::Create($action)
                return $this.Kuhul.Execute($script)
            }
            params = @{ action = @{ type = "string"; description = "Action to execute" } }
        }
        
        $this.Tools["kuhul_project"] = @{
            name = "kuhul_project"
            description = "K'UHUL Ch'en: Project output"
            handler = {
                param($params)
                $target = $params.target ?? "result"
                return $this.Kuhul.Project($target)
            }
            params = @{ target = @{ type = "string"; description = "Target to project" } }
        }
        
        $this.Tools["kuhul_consolidate"] = @{
            name = "kuhul_consolidate"
            description = "K'UHUL Xul: Consolidate state"
            handler = {
                param($params)
                return $this.Kuhul.Consolidate()
            }
            params = @{}
        }
        
        $this.Tools["kuhul_reflect"] = @{
            name = "kuhul_reflect"
            description = "K'UHUL Noj: Reflect on state"
            handler = {
                param($params)
                return $this.Kuhul.Reflect()
            }
            params = @{}
        }
        
        $this.Tools["kuhul_enforce"] = @{
            name = "kuhul_enforce"
            description = "Enforce a K'UHUL law"
            handler = {
                param($params)
                $lawName = $params.law
                $state = $params.state ?? $null
                return $this.Kuhul.EnforceLaw($lawName, $state)
            }
            params = @{
                law = @{ type = "string"; description = "Law name" }
                state = @{ type = "object"; description = "State to enforce" }
            }
        }
        
        # Micronaut Tools
        $this.Tools["micronaut_status"] = @{
            name = "micronaut_status"
            description = "Get Micronaut status"
            handler = {
                param($params)
                return $this.Micronaut.GetStatus()
            }
            params = @{}
        }
        
        $this.Tools["micronaut_execute_fold"] = @{
            name = "micronaut_execute_fold"
            description = "Execute a Micronaut fold"
            handler = {
                param($params)
                $foldName = $params.fold
                $input = $params.input ?? @{}
                return $this.Micronaut.ExecuteFold($foldName, $input)
            }
            params = @{
                fold = @{ type = "string"; description = "Fold name" }
                input = @{ type = "object"; description = "Input data" }
            }
        }
        
        $this.Tools["micronaut_agent_execute"] = @{
            name = "micronaut_agent_execute"
            description = "Execute an agent tool"
            handler = {
                param($params)
                $agentName = $params.agent
                $toolName = $params.tool
                $input = $params.input ?? @{}
                $agent = $this.Micronaut.Agents | Where-Object { $_.Identity.Name -eq $agentName }
                if (-not $agent) {
                    throw "Agent '$agentName' not found"
                }
                return $agent.Execute($toolName, $input)
            }
            params = @{
                agent = @{ type = "string"; description = "Agent name" }
                tool = @{ type = "string"; description = "Tool name" }
                input = @{ type = "object"; description = "Input data" }
            }
        }
        
        # Original tools preserved
        $this.Tools["echo"] = @{
            name = "echo"
            description = "Echo back a message"
            handler = {
                param($params)
                return @{ echo = $params.message ?? "No message provided" }
            }
            params = @{ message = @{ type = "string"; description = "Message to echo" } }
        }
        
        $this.Tools["dns_lookup"] = @{
            name = "dns_lookup"
            description = "Look up DNS records"
            handler = {
                param($params)
                $domain = $params.domain
                if (-not $domain) { throw "Domain required" }
                $dns = [DnsResolver]::new([FileCache]::new($CacheDir))
                return $dns.Resolve($domain, $params.type ?? "A")
            }
            params = @{
                domain = @{ type = "string"; description = "Domain to look up" }
                type = @{ type = "string"; description = "Record type" }
            }
        }
        
        $this.Tools["cache_stats"] = @{
            name = "cache_stats"
            description = "Get cache statistics"
            handler = {
                param($params)
                $cache = [FileCache]::new($CacheDir)
                return $cache.Stats()
            }
            params = @{}
        }
    }
    
    [void]RegisterDefaultResources() {
        $this.Resources["info://server"] = @{
            uri = "info://server"
            name = "Server Info"
            mimeType = "application/json"
            handler = {
                return @{
                    name = "PHPServer.ps1 · K'UHUL π · Micronaut µ"
                    version = "3.0.0"
                    kuhul = @{
                        phase = $this.Kuhul.CurrentPhase.Glyph
                        laws = $this.Kuhul.Laws.Keys
                        history = $this.Kuhul.PhaseHistory.Count
                    }
                    micronaut = @{
                        name = $this.Micronaut.Identity.Name
                        status = $this.Micronaut.State.Status
                        folds = $this.Micronaut.Folds.Keys
                        agents = $this.Micronaut.Agents.Count
                    }
                    timestamp = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ss.fffZ")
                } | ConvertTo-Json -Depth 10
            }
        }
        
        $this.Resources["info://kuhul"] = @{
            uri = "info://kuhul"
            name = "K'UHUL State"
            mimeType = "application/json"
            handler = {
                return $this.Kuhul.GetState() | ConvertTo-Json -Depth 10
            }
        }
        
        $this.Resources["info://micronaut"] = @{
            uri = "info://micronaut"
            name = "Micronaut State"
            mimeType = "application/json"
            handler = {
                return $this.Micronaut.GetStatus() | ConvertTo-Json -Depth 10
            }
        }
    }
    
    [void]RegisterDefaultPrompts() {
        $this.Prompts["kuhul_phase"] = @{
            name = "kuhul_phase"
            description = "K'UHUL phase guidance"
            handler = {
                param($params)
                $phase = $params.phase ?? "Pop"
                return @{
                    phase = $phase
                    meaning = [KuhulPhase]::MEANINGS[$phase]
                    current = $this.Kuhul.CurrentPhase.Glyph
                    history = $this.Kuhul.PhaseHistory | ForEach-Object { $_.Glyph }
                    guidance = @{
                        Pop = "Begin by perceiving the input"
                        Wo = "Represent the data structure"
                        Yax = "Plan the intention"
                        Sek = "Execute the action"
                        Ch'en = "Project the output"
                        Xul = "Consolidate the result"
                        Noj = "Reflect on the process"
                    }[$phase]
                }
            }
            params = @{ phase = @{ type = "string"; description = "Phase to guide" } }
        }
        
        $this.Prompts["micronaut_orchestrate"] = @{
            name = "micronaut_orchestrate"
            description = "Micronaut orchestration guidance"
            handler = {
                param($params)
                $fold = $params.fold ?? "default"
                return @{
                    orchestration = "Micronaut orchestrates contexts."
                    fold = $fold
                    available = $this.Micronaut.Folds.Keys
                    status = $this.Micronaut.State.Status
                    policy = $this.Micronaut.Policy.ToHash()
                    guidance = "SELECT, ARRANGE, CHOOSE, MANAGE — Micronaut selects fields, arranges collapse timing, chooses field selections, and manages host reality."
                }
            }
            params = @{ fold = @{ type = "string"; description = "Fold to orchestrate" } }
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
            "kuhul/phase" { return $this.HandleKuhulPhase($message) }
            "kuhul/law" { return $this.HandleKuhulLaw($message) }
            "micronaut/fold" { return $this.HandleMicronautFold($message) }
            "micronaut/agent" { return $this.HandleMicronautAgent($message) }
            default { return $this.ErrorResponse("Unknown MCP message type: $($message.type)") }
        }
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
                kuhul = $true
                micronaut = $true
            }
        }
    }
    
    [object]HandleToolsList($message) {
        $tools = @()
        foreach ($name in $this.Tools.Keys) {
            $tool = $this.Tools[$name]
            $tools += @{
                name = $name
                description = $tool.description
                parameters = $tool.params
                version = "1.0.0"
            }
        }
        return @{ type = "tools/list_response"; tools = $tools }
    }
    
    [object]HandleToolsCall($message) {
        $toolName = $message.name
        $params = $message.parameters ?? @{}
        
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
        $resources = @()
        foreach ($uri in $this.Resources.Keys) {
            $resource = $this.Resources[$uri]
            $resources += @{
                uri = $uri
                name = $resource.name
                mimeType = $resource.mimeType
            }
        }
        return @{ type = "resources/list_response"; resources = $resources }
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
        $prompts = @()
        foreach ($name in $this.Prompts.Keys) {
            $prompt = $this.Prompts[$name]
            $prompts += @{
                name = $name
                description = $prompt.description
                parameters = $prompt.params
            }
        }
        return @{ type = "prompts/list_response"; prompts = $prompts }
    }
    
    [object]HandlePromptsGet($message) {
        $name = $message.name
        $params = $message.parameters ?? @{}
        
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
    
    [object]HandleKuhulPhase($message) {
        $phase = $message.phase ?? "Pop"
        try {
            $p = [KuhulPhase]::new($phase)
            return @{
                type = "kuhul/phase_response"
                phase = $phase
                meaning = $p.Value
                index = $p.Index
                valid_transitions = @{
                    from = $phase
                    to = @($phase, [KuhulPhase]::new($phase).IsAfter([KuhulPhase]::new("Pop")) ? "Next phase" : "Same phase")
                }
                current = $this.Kuhul.CurrentPhase.Glyph
            }
        } catch {
            return $this.ErrorResponse($_.Exception.Message)
        }
    }
    
    [object]HandleKuhulLaw($message) {
        $lawName = $message.name
        $definition = $message.definition ?? @{
            phases = @("Pop", "Wo", "Yax", "Sek", "Ch'en", "Xul")
            invariants = @{
                "collapse_only" = $true
                "field_perception" = $true
                "compression_law" = $true
            }
        }
        
        try {
            $law = [KuhulLaw]::new($lawName, $definition)
            $this.Kuhul.DefineLaw($lawName, $definition)
            return @{
                type = "kuhul/law_response"
                name = $lawName
                hash = $law.Hash
                phases = $law.Phases | ForEach-Object { $_.Glyph }
                enforced = $true
            }
        } catch {
            return $this.ErrorResponse($_.Exception.Message)
        }
    }
    
    [object]HandleMicronautFold($message) {
        $foldName = $message.name
        $type = $message.type ?? "compute"
        
        try {
            $this.Micronaut.CreateFold($foldName, $type)
            $this.Micronaut.Orchestrate($foldName)
            return @{
                type = "micronaut/fold_response"
                name = $foldName
                type = $type
                created = $true
                folds = $this.Micronaut.Folds.Keys
            }
        } catch {
            return $this.ErrorResponse($_.Exception.Message)
        }
    }
    
    [object]HandleMicronautAgent($message) {
        $agentName = $message.name
        $type = $message.type ?? "worker"
        
        try {
            $agent = [MicronautAgent]::new($agentName, $type)
            $this.Micronaut.AddAgent($agent)
            return @{
                type = "micronaut/agent_response"
                name = $agentName
                type = $type
                created = $true
                agents = $this.Micronaut.Agents.Count
            }
        } catch {
            return $this.ErrorResponse($_.Exception.Message)
        }
    }
    
    [object]ErrorResponse([string]$message, [int]$code = -1) {
        return @{
            type = "error"
            error = @{ code = $code; message = $message }
        }
    }
}

#region Cache & DNS (Original Components)

class FileCache {
    [string]$CacheDir
    [int]$DefaultTTL
    
    FileCache([string]$cacheDir, [int]$defaultTTL = 3600) {
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
            Write-Log "DNS lookup failed for $domain: $($_.Exception.Message)" "WARNING" "SERVER"
        }
        return @()
    }
}

#region JSON-RPC Server (Enhanced)

class JsonRpcServerEnhanced {
    [hashtable]$Methods
    [hashtable]$Notifications
    [KuhulRuntime]$Kuhul
    [Micronaut]$Micronaut
    
    JsonRpcServerEnhanced([KuhulRuntime]$kuhul, [Micronaut]$micronaut) {
        $this.Methods = @{}
        $this.Notifications = @{}
        $this.Kuhul = $kuhul
        $this.Micronaut = $micronaut
        $this.RegisterDefaultMethods()
    }
    
    [void]RegisterMethod([string]$method, [scriptblock]$handler) {
        $this.Methods[$method] = $handler
    }
    
    [void]RegisterNotification([string]$method, [scriptblock]$handler) {
        $this.Notifications[$method] = $handler
    }
    
    [object]Handle($request) {
        if ($request -is [string]) {
            $request = $request | ConvertFrom-Json
        }
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
        $error = $this.ValidateRequest($request)
        if ($error) {
            return $error
        }
        
        $method = $request.method
        $params = $request.params
        $id = $request.id
        $isNotification = -not $request.PSObject.Properties.Name -contains "id"
        
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
        # Original methods
        $this.RegisterMethod("system.info", {
            return @{
                server = "PHPServer.ps1 · K'UHUL π · Micronaut µ"
                version = "3.0.0"
                kuhul = @{
                    phase = $this.Kuhul.CurrentPhase.Glyph
                    laws = $this.Kuhul.Laws.Keys
                }
                micronaut = @{
                    status = $this.Micronaut.State.Status
                    folds = $this.Micronaut.Folds.Keys
                    agents = $this.Micronaut.Agents.Count
                }
            }
        })
        
        $this.RegisterMethod("system.echo", { param($params)
            return $params.message ?? $params[0] ?? "No message"
        })
        
        $this.RegisterMethod("system.ping", {
            return @{ pong = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ss.fffZ") }
        })
        
        # K'UHUL RPC methods
        $this.RegisterMethod("kuhul.phase", { param($params)
            $phase = $params.phase ?? "Pop"
            $p = [KuhulPhase]::new($phase)
            return @{
                phase = $phase
                meaning = $p.Value
                index = $p.Index
                current = $this.Kuhul.CurrentPhase.Glyph
                history = $this.Kuhul.PhaseHistory | ForEach-Object { $_.Glyph }
            }
        })
        
        $this.RegisterMethod("kuhul.state", {
            return $this.Kuhul.GetState()
        })
        
        $this.RegisterMethod("kuhul.enforce", { param($params)
            $lawName = $params.law
            if (-not $lawName) { throw "Law name required" }
            $state = $params.state ?? $null
            return $this.Kuhul.EnforceLaw($lawName, $state)
        })
        
        $this.RegisterMethod("kuhul.define", { param($params)
            $lawName = $params.name
            $definition = $params.definition ?? @{
                phases = @("Pop", "Wo", "Yax", "Sek", "Ch'en", "Xul")
                invariants = @{
                    "collapse_only" = $true
                    "field_perception" = $true
                    "compression_law" = $true
                    "unreachable_states" = $true
                }
            }
            $this.Kuhul.DefineLaw($lawName, $definition)
            return @{ defined = $lawName; hash = $this.Kuhul.Laws[$lawName].Hash.Substring(0,8) }
        })
        
        # Micronaut RPC methods
        $this.RegisterMethod("micronaut.status", {
            return $this.Micronaut.GetStatus()
        })
        
        $this.RegisterMethod("micronaut.fold.create", { param($params)
            $name = $params.name
            $type = $params.type ?? "compute"
            if (-not $name) { throw "Fold name required" }
            $this.Micronaut.CreateFold($name, $type)
            $this.Micronaut.Orchestrate($name)
            return @{ created = $name; type = $type; folds = $this.Micronaut.Folds.Keys }
        })
        
        $this.RegisterMethod("micronaut.fold.execute", { param($params)
            $name = $params.name
            $input = $params.input ?? @{}
            if (-not $name) { throw "Fold name required" }
            return $this.Micronaut.ExecuteFold($name, $input)
        })
        
        $this.RegisterMethod("micronaut.agent.create", { param($params)
            $name = $params.name
            $type = $params.type ?? "worker"
            if (-not $name) { throw "Agent name required" }
            $agent = [MicronautAgent]::new($name, $type)
            $this.Micronaut.AddAgent($agent)
            return @{ created = $name; type = $type; agents = $this.Micronaut.Agents.Count }
        })
        
        $this.RegisterMethod("micronaut.agent.execute", { param($params)
            $agentName = $params.agent
            $toolName = $params.tool
            $input = $params.input ?? @{}
            if (-not $agentName) { throw "Agent name required" }
            if (-not $toolName) { throw "Tool name required" }
            $agent = $this.Micronaut.Agents | Where-Object { $_.Identity.Name -eq $agentName }
            if (-not $agent) { throw "Agent '$agentName' not found" }
            return $agent.Execute($toolName, $input)
        })
        
        # Cache methods
        $this.RegisterMethod("cache.get", { param($params)
            $key = $params.key ?? $params[0]
            if (-not $key) { throw "Cache key required" }
            $cache = [FileCache]::new($CacheDir)
            return $cache.Get($key)
        })
        
        $this.RegisterMethod("cache.set", { param($params)
            $key = $params.key ?? $params[0]
            $value = $params.value ?? $params[1]
            $ttl = $params.ttl ?? $params[2]
            if (-not $key -or $value -eq $null) { throw "Key and value required" }
            $cache = [FileCache]::new($CacheDir)
            $cache.Set($key, $value, $ttl)
            return $true
        })
        
        $this.RegisterMethod("dns.resolve", { param($params)
            $domain = $params.domain ?? $params[0]
            $type = $params.type ?? "A"
            if (-not $domain) { throw "Domain required" }
            $dns = [DnsResolver]::new([FileCache]::new($CacheDir))
            return $dns.Resolve($domain, $type)
        })
    }
}

#region HTTP Server (Enhanced)

class HttpServerEnhanced {
    [string]$DocumentRoot
    [int]$Port
    [hashtable]$Routes
    [array]$Middleware
    [FileCache]$Cache
    [JsonRpcServerEnhanced]$JsonRpc
    [McpServerEnhanced]$Mcp
    [DnsResolver]$Dns
    [KuhulRuntime]$Kuhul
    [Micronaut]$Micronaut
    
    HttpServerEnhanced([int]$port, [string]$documentRoot, [string]$cacheDir) {
        $this.Port = $port
        $this.DocumentRoot = $documentRoot
        $this.Routes = @{}
        $this.Middleware = @()
        $this.Cache = [FileCache]::new($cacheDir)
        
        # Initialize K'UHUL
        $this.Kuhul = [KuhulRuntime]::new()
        
        # Define default laws
        $this.Kuhul.DefineLaw("collapse_only", @{
            phases = @("Pop", "Wo", "Yax", "Sek", "Ch'en", "Xul")
            invariants = @{
                "collapse_only" = $true
                "field_perception" = $true
                "compression_law" = $true
                "unreachable_states" = $true
            }
        })
        
        $this.Kuhul.DefineLaw("perception_field", @{
            phases = @("Pop", "Wo", "Xul")
            invariants = @{
                "field_perception" = $true
                "collapse_only" = $true
            }
        })
        
        # Initialize Micronaut
        $this.Micronaut = [Micronaut]::new("PrimaryOrchestrator", "orchestrator")
        $this.Micronaut.CreateFold("compute", "compute")
        $this.Micronaut.Orchestrate("compute")
        $this.Micronaut.CreateFold("reasoning", "reasoning")
        $this.Micronaut.Orchestrate("reasoning")
        
        # Create some fields
        $this.Micronaut.CreateField("working", "working", "volatile")
        $this.Micronaut.CreateField("episodic", "episodic", "persistent")
        
        # Create an agent
        $agent = [MicronautAgent]::new("Assistant", "helper")
        $agent.AddTool("echo")
        $agent.AddTool("dns_lookup")
        $agent.AddGoal("Help users with their requests", 1.0)
        $agent.AddConstraint("Be helpful and accurate")
        $this.Micronaut.AddAgent($agent)
        
        # Initialize MCP
        $this.Mcp = [McpServerEnhanced]::new($this.Kuhul, $this.Micronaut)
        
        # Initialize JSON-RPC
        $this.JsonRpc = [JsonRpcServerEnhanced]::new($this.Kuhul, $this.Micronaut)
        
        $this.Dns = [DnsResolver]::new($this.Cache)
        $this.RegisterDefaultRoutes()
        
        Write-Log "⟁ K'UHUL π enforced" "PHASE" "KUHUL"
        Write-Log "µ Micronaut orchestrating" "ORCHESTRATE" "MICRONAUT"
        Write-Log "⚡ MCP ready" "PHASE" "MCP"
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
        
        # K'UHUL perceives the request
        $this.Kuhul.Perceive(@{
            method = $method
            path = $path
            input = $input
            timestamp = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ss.fffZ")
        })
        
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
            $result = $this.HandleRest($method, $path, $input)
            $this.Kuhul.Project("result")
            return $result
        }
        
        # Check RPC prefix
        if ($path.StartsWith("/api/rpc")) {
            $result = $this.HandleRpc($input)
            $this.Kuhul.Project("rpc_result")
            return $result
        }
        
        # Check MCP prefix
        if ($path.StartsWith("/mcp")) {
            $result = $this.HandleMcp($input)
            $this.Kuhul.Project("mcp_result")
            return $result
        }
        
        # Check K'UHUL endpoint
        if ($path.StartsWith("/kuhul")) {
            $result = $this.HandleKuhul($method, $path, $input)
            $this.Kuhul.Project("kuhul_result")
            return $result
        }
        
        # Check Micronaut endpoint
        if ($path.StartsWith("/micronaut")) {
            $result = $this.HandleMicronaut($method, $path, $input)
            $this.Kuhul.Project("micronaut_result")
            return $result
        }
        
        # Try route matching
        $key = "$method`:$path"
        if ($this.Routes.ContainsKey($key)) {
            $handler = $this.Routes[$key]
            $result = & $handler $input
            $this.Kuhul.Project("route_result")
            return $result
        }
        
        # Serve static file
        $result = $this.ServeStaticFile($path)
        $this.Kuhul.Consolidate()
        return $result
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
    
    [object]HandleKuhul([string]$method, [string]$path, $input) {
        switch ($method) {
            "GET" {
                return @{
                    kuhul = $true
                    phase = $this.Kuhul.CurrentPhase.Glyph
                    phases = @("Pop", "Wo", "Yax", "Sek", "Ch'en", "Xul", "Noj")
                    meaning = [KuhulPhase]::MEANINGS
                    laws = $this.Kuhul.Laws.Keys
                    state = $this.Kuhul.GetState()
                    history = $this.Kuhul.PhaseHistory | ForEach-Object { $_.Glyph }
                    version = "π 3.0.0"
                    invariant = "Micronaut orchestrates. K'UHUL enforces. They are orthogonal."
                }
            }
            "POST" {
                if ($input -and $input.type) {
                    switch ($input.type) {
                        "phase" {
                            $phase = $input.phase ?? "Pop"
                            $p = [KuhulPhase]::new($phase)
                            $this.Kuhul.TransitionPhase($phase)
                            return @{
                                transitioned = $true
                                from = $this.Kuhul.PhaseHistory[-1].Glyph
                                to = $phase
                                meaning = $p.Value
                            }
                        }
                        "law" {
                            $lawName = $input.name
                            $definition = $input.definition
                            $this.Kuhul.DefineLaw($lawName, $definition)
                            return @{
                                defined = $lawName
                                hash = $this.Kuhul.Laws[$lawName].Hash.Substring(0,8)
                            }
                        }
                        "enforce" {
                            $lawName = $input.law
                            $state = $input.state
                            return @{
                                enforced = $this.Kuhul.EnforceLaw($lawName, $state)
                                state = $this.Kuhul.GetState()
                            }
                        }
                        default {
                            return @{ error = "Unknown K'UHUL operation: $($input.type)" }
                        }
                    }
                }
                return @{ error = "Invalid K'UHUL request" }
            }
            default {
                return @{ error = "Method not allowed for K'UHUL" }
            }
        }
    }
    
    [object]HandleMicronaut([string]$method, [string]$path, $input) {
        switch ($method) {
            "GET" {
                return @{
                    micronaut = $true
                    status = $this.Micronaut.GetStatus()
                    version = "µ 3.0.0"
                    invariant = "Micronaut orchestrates contexts. K'UHUL enforces law."
                }
            }
            "POST" {
                if ($input -and $input.type) {
                    switch ($input.type) {
                        "fold" {
                            $name = $input.name
                            $type = $input.type_fold ?? "compute"
                            $this.Micronaut.CreateFold($name, $type)
                            $this.Micronaut.Orchestrate($name)
                            return @{
                                created = $name
                                type = $type
                                folds = $this.Micronaut.Folds.Keys
                            }
                        }
                        "execute_fold" {
                            $name = $input.name
                            $params = $input.params ?? @{}
                            return $this.Micronaut.ExecuteFold($name, $params)
                        }
                        "agent" {
                            $name = $input.name
                            $type_agent = $input.type_agent ?? "worker"
                            $agent = [MicronautAgent]::new($name, $type_agent)
                            foreach ($tool in $input.tools ?? @()) {
                                $agent.AddTool($tool)
                            }
                            $this.Micronaut.AddAgent($agent)
                            return @{
                                created = $name
                                type = $type_agent
                                agents = $this.Micronaut.Agents.Count
                            }
                        }
                        "field" {
                            $name = $input.name
                            $type_field = $input.type_field ?? "working"
                            $persistence = $input.persistence ?? "volatile"
                            $this.Micronaut.CreateField($name, $type_field, $persistence)
                            return @{
                                created = $name
                                type = $type_field
                                persistence = $persistence
                                fields = $this.Micronaut.Fields.Keys
                            }
                        }
                        default {
                            return @{ error = "Unknown Micronaut operation: $($input.type)" }
                        }
                    }
                }
                return @{ error = "Invalid Micronaut request" }
            }
            default {
                return @{ error = "Method not allowed for Micronaut" }
            }
        }
    }
    
    [object]ServeStaticFile([string]$path) {
        $filePath = Join-Path $this.DocumentRoot $path
        if (Test-Path $filePath -PathType Leaf) {
            $content = Get-Content -Path $filePath -Raw
            return $content
        }
        $indexPath = Join-Path $this.DocumentRoot "index.php"
        if (Test-Path $indexPath) {
            return $this.ExecutePhpFile($indexPath)
        }
        return @{ error = "Not Found"; path = $path }
    }
    
    [string]ExecutePhpFile([string]$path) {
        try {
            $content = Get-Content -Path $path -Raw -Encoding UTF8
            $output = "<h1>PHP Execution</h1><pre>"
            $output += "K'UHUL π: " + $this.Kuhul.CurrentPhase.ToString() + "`n"
            $output += "Micronaut µ: " + $this.Micronaut.State.Status + "`n"
            $output += "PHP Content length: " + $content.Length + " bytes`n"
            $output += "=== PHP Output ===`n"
            $output += $content
            $output += "`n=== K'UHUL Reflection ===`n"
            $reflection = $this.Kuhul.Reflect()
            $output += "Phases: " + ($reflection.reflection.phases) + "`n"
            $output += "Current: " + ($reflection.reflection.current_phase) + "`n"
            $output += "Fields: " + ($reflection.reflection.fields) + "`n"
            $output += "Laws: " + ($reflection.reflection.laws) + "`n"
            $output += "</pre>"
            return $output
        } catch {
            Write-Log "PHP execution error: $($_.Exception.Message)" "ERROR" "PHP"
            return "<h1>PHP Error</h1><p>$($_.Exception.Message)</p>"
        }
    }
    
    [void]RegisterDefaultRoutes() {
        $this.RegisterRoute("GET", "/", {
            return @{
                service = "PHPServer.ps1 · K'UHUL π · Micronaut µ"
                version = "3.0.0"
                architecture = @{
                    orchestrator = "Micronaut"
                    enforcer = "K'UHUL π"
                    boundary = "They are orthogonal. The boundary is permanent."
                }
                endpoints = @(
                    @{ path = "/"; method = "GET"; description = "Service info" }
                    @{ path = "/health"; method = "GET"; description = "Health check" }
                    @{ path = "/kuhul"; method = "GET|POST"; description = "K'UHUL π enforcement" }
                    @{ path = "/micronaut"; method = "GET|POST"; description = "Micronaut µ orchestration" }
                    @{ path = "/api/v1/dns"; method = "GET"; description = "DNS lookup" }
                    @{ path = "/api/v1/cache/stats"; method = "GET"; description = "Cache statistics" }
                    @{ path = "/api/rpc"; method = "POST"; description = "JSON-RPC endpoint" }
                    @{ path = "/mcp"; method = "POST"; description = "MCP endpoint" }
                )
                phases = @{
                    glyphs = @("Pop", "Wo", "Yax", "Sek", "Ch'en", "Xul", "Noj")
                    current = $this.Kuhul.CurrentPhase.Glyph
                    meaning = [KuhulPhase]::MEANINGS
                }
                micronaut = @{
                    status = $this.Micronaut.State.Status
                    folds = $this.Micronaut.Folds.Keys
                    agents = $this.Micronaut.Agents.Count
                    fields = $this.Micronaut.Fields.Keys
                }
                canonical = @(
                    "Micronaut orchestrates contexts.",
                    "KUHUL π enforces law.",
                    "Extrapolator expands without altering outcomes.",
                    "They are orthogonal.",
                    "The boundary is permanent.",
                    "No further refinement possible."
                )
            }
        })
        
        $this.RegisterRoute("GET", "/health", {
            return @{
                status = "ok"
                timestamp = (Get-Date).ToUniversalTime().ToString("yyyy-MM-ddTHH:mm:ss.fffZ")
                kuhul = @{
                    phase = $this.Kuhul.CurrentPhase.Glyph
                    enforcing = $this.Kuhul.IsEnforcing
                    laws = $this.Kuhul.Laws.Count
                }
                micronaut = @{
                    status = $this.Micronaut.State.Status
                    coherence = $this.Micronaut.State.Coherence
                    entropy = $this.Micronaut.State.Entropy
                }
                cache = $this.Cache.Stats()
            }
        })
        
        $this.RegisterRoute("GET", "/api/v1/cache/stats", {
            return $this.Cache.Stats()
        })
        
        $this.RegisterRoute("DELETE", "/api/v1/cache", {
            $this.Cache.Clear()
            return @{ cleared = $true }
        })
        
        $this.RegisterRoute("GET", "/api/v1/dns", {
            param($input)
            $domain = [System.Web.HttpUtility]::ParseQueryString([System.Uri]::new("http://localhost$input").Query)["domain"]
            $type = [System.Web.HttpUtility]::ParseQueryString([System.Uri]::new("http://localhost$input").Query)["type"] ?? "A"
            if (-not $domain) {
                return @{ error = "Domain parameter required" }
            }
            return $this.Dns.Resolve($domain, $type)
        })
        
        $this.RegisterRoute("GET", "/api/v1/kuhul/info", {
            return @{
                glyphs = [KuhulPhase]::GLYPHS
                meanings = [KuhulPhase]::MEANINGS
                current = $this.Kuhul.CurrentPhase.Glyph
                laws = $this.Kuhul.Laws.Keys
                state = $this.Kuhul.GetState()
                history = $this.Kuhul.PhaseHistory | ForEach-Object { $_.Glyph }
                invariant = "K'UHUL cannot orchestrate. Micronaut cannot enforce."
            }
        })
        
        $this.RegisterRoute("GET", "/api/v1/micronaut/info", {
            return $this.Micronaut.GetStatus()
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

Write-Log "⟁ Initializing K'UHUL π & Micronaut µ Supercharged Server ⟁" "PHASE" "KUHUL"

$server = [HttpServerEnhanced]::new($Port, $DocumentRoot, $CacheDir)

$server.AddMiddleware({
    param($context)
    Write-Log "◆ $($context.method) $($context.path)" "INFO" "SERVER"
    return $null
})

Write-Log "◆ All components initialized" "INFO" "SERVER"
Write-Log "π K'UHUL phase: $($server.Kuhul.CurrentPhase.ToString())" "PHASE" "KUHUL"
Write-Log "µ Micronaut status: $($server.Micronaut.State.Status)" "ORCHESTRATE" "MICRONAUT"
Write-Log "⚡ MCP capabilities: Tools($($server.Mcp.Tools.Count)), Resources($($server.Mcp.Resources.Count)), Prompts($($server.Mcp.Prompts.Count))" "INFO" "MCP"

Write-Log "⟁ Server starting on http://localhost:$Port ⟁" -ForegroundColor Green
Write-Log "π K'UHUL endpoint: http://localhost:$Port/kuhul" -ForegroundColor Cyan
Write-Log "µ Micronaut endpoint: http://localhost:$Port/micronaut" -ForegroundColor Cyan
Write-Log "⚡ MCP endpoint: http://localhost:$Port/mcp" -ForegroundColor Cyan
Write-Log "🐘 JSON-RPC endpoint: http://localhost:$Port/api/rpc" -ForegroundColor Cyan
Write-Log "◆ REST API: http://localhost:$Port/api/v1/*" -ForegroundColor Cyan
Write-Log "◆ Health: http://localhost:$Port/health" -ForegroundColor Cyan

Write-Log "⟁ Press Ctrl+C to stop the server ⟁" -ForegroundColor Yellow
Write-Log "▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬" -ForegroundColor Gray

$listener = [System.Net.HttpListener]::new()
$listener.Prefixes.Add("http://localhost:$Port/")
$listener.Start()

$cts = [System.Threading.CancellationTokenSource]::new()
$token = $cts.Token

[Console]::TreatControlCAsInput = $false
Register-EngineEvent -SourceIdentifier PowerShell.Exiting -Action {
    Write-Log "⟁ Shutting down server... ⟁" "WARNING" "SERVER"
    $cts.Cancel()
} | Out-Null

while ($listener.IsListening -and -not $token.IsCancellationRequested) {
    try {
        $context = $listener.GetContextAsync().GetAwaiter().GetResult()
        
        $task = [System.Threading.Tasks.Task]::Run({
            param($ctx)
            try {
                $request = $ctx.Request
                $response = $ctx.Response
                
                $method = $request.HttpMethod
                $uri = $request.RawUrl
                
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
                
                $result = $server.Dispatch($method, $uri, $input)
                
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
                Write-Log "◆ Error processing request: $($_.Exception.Message)" "ERROR" "SERVER"
                try {
                    $ctx.Response.StatusCode = 500
                    $ctx.Response.ContentType = "application/json"
                    $errorMsg = @{ error = "Internal Server Error" } | ConvertTo-Json
                    $bytes = [System.Text.Encoding]::UTF8.GetBytes($errorMsg)
                    $ctx.Response.OutputStream.Write($bytes, 0, $bytes.Length)
                    $ctx.Response.OutputStream.Close()
                } catch {}
            }
        }, $context)
        
    } catch {
        if (-not $token.IsCancellationRequested) {
            Write-Log "◆ Error accepting connection: $($_.Exception.Message)" "ERROR" "SERVER"
        }
    }
}

$listener.Stop()
$listener.Close()

Write-Log "⟁ Server stopped. K'UHUL π consolidated. Micronaut µ terminated. ⟁" "WARNING" "SERVER"
