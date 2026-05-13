@props(['steps' => []])

@php
    $nodeWidth = 280;
    $nodeHeight = 200;
    $horizontalGap = 80;
    $verticalGap = 60;
    $nodesPerRow = 3;

    $deviceConfig = [
        'dinamo_x' => ['name' => 'Dinamo X', 'color' => 'from-cyan-500 to-blue-600', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
        'dinamo_y' => ['name' => 'Dinamo Y', 'color' => 'from-emerald-500 to-teal-600', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
        'relay_pump' => ['name' => 'Pompa Air', 'color' => 'from-violet-500 to-purple-600', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
        'servo_nozzle' => ['name' => 'Servo Nozzle', 'color' => 'from-pink-500 to-rose-600', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15']
    ];

    $rows = ceil(count($steps) / $nodesPerRow);
    $canvasWidth = 100 + ($nodesPerRow * ($nodeWidth + $horizontalGap));
    $canvasHeight = 100 + ($rows * ($nodeHeight + $verticalGap));
@endphp

<div class="relative rounded-xl border border-cyan-500/20 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 overflow-hidden"
     id="flow-canvas"
     style="min-height: {{ $canvasHeight }}px; height: auto; min-width: 100%; backdrop-filter: blur(10px);">

    <!-- Grid background -->
    <div class="absolute inset-0 pointer-events-none opacity-20"
         style="background-image:
                  linear-gradient(0deg, transparent 24%, rgba(34, 211, 238, 0.05) 25%, rgba(34, 211, 238, 0.05) 26%, transparent 27%, transparent 74%, rgba(34, 211, 238, 0.05) 75%, rgba(34, 211, 238, 0.05) 76%, transparent 77%, transparent),
                  linear-gradient(90deg, transparent 24%, rgba(34, 211, 238, 0.05) 25%, rgba(34, 211, 238, 0.05) 26%, transparent 27%, transparent 74%, rgba(34, 211, 238, 0.05) 75%, rgba(34, 211, 238, 0.05) 76%, transparent 77%, transparent);
          background-size: 50px 50px;">
    </div>

    <!-- SVG Canvas for edges -->
    <svg class="absolute inset-0 pointer-events-none" id="flow-lines" style="z-index: 5;">
        <defs>
            <linearGradient id="lightningGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#06b6d4" stop-opacity="0.8"/>
                <stop offset="50%" stop-color="#0ea5e9" stop-opacity="1"/>
                <stop offset="100%" stop-color="#06b6d4" stop-opacity="0.8"/>
            </linearGradient>
            <filter id="lightGlow">
                <feGaussianBlur stdDeviation="2" result="coloredBlur"/>
                <feMerge>
                    <feMergeNode in="coloredBlur"/>
                    <feMergeNode in="SourceGraphic"/>
                </feMerge>
            </filter>
        </defs>
    </svg>

    <!-- Nodes container -->
    <div id="flow-builder-steps" class="relative" style="z-index: 10;">
        @if(count($steps) === 0)
            <div class="flex items-center justify-center" style="min-height: 400px;">
                <div class="rounded-xl border border-cyan-500/30 bg-slate-900/50 backdrop-blur-sm p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-cyan-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <p class="mt-4 text-sm font-semibold text-cyan-400/70">Belum ada step</p>
                    <p class="mt-2 text-xs text-cyan-400/50">Klik "Tambah Step" untuk memulai</p>
                </div>
            </div>
        @else
            @foreach($steps as $index => $step)
                @php
                    $row = floor($index / $nodesPerRow);
                    $col = $index % $nodesPerRow;
                    $x = 50 + ($col * ($nodeWidth + $horizontalGap));
                    $y = 50 + ($row * ($nodeHeight + $verticalGap));
                    $device = $deviceConfig[$step['device']] ?? $deviceConfig['dinamo_x'];
                @endphp

                <div class="flow-node group absolute rounded-xl border border-cyan-500/40 bg-gradient-to-br from-slate-800/80 to-slate-900/80 shadow-2xl backdrop-blur-md transition-all duration-300 hover:border-cyan-400/80 hover:shadow-[0_0_30px_rgba(34,211,238,0.4)]"
                     style="left: {{ $x }}px; top: {{ $y }}px; width: {{ $nodeWidth }}px; backdrop-filter: blur(10px);"
                     data-index="{{ $index }}"
                     data-node-id="node-{{ $index }}">

                    <!-- Glow aura -->
                    <div class="absolute inset-0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                         style="background: radial-gradient(circle at 50% 50%, rgba(34, 211, 238, 0.2) 0%, transparent 70%); pointer-events: none;"></div>

                    <!-- Header -->
                    <div class="relative flex items-center justify-between bg-gradient-to-r {{ $device['color'] }} rounded-t-lg p-3 cursor-move node-header group/header hover:shadow-lg transition-all"
                         data-draggable="true">
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 backdrop-blur-sm group-hover/header:bg-white/20 transition-all">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $device['icon'] }}"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white">{{ $device['name'] }}</p>
                                <p class="text-xs text-white/70">Step {{ $index + 1 }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="removeFlowStep({{ $index }})" class="rounded p-1 text-white/60 hover:bg-white/20 hover:text-white transition-all">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="relative p-3 space-y-2">
                        <select onchange="updateStepField({{ $index }}, 'device', this.value)" class="w-full rounded-lg border border-cyan-500/30 bg-slate-800/50 px-2 py-1.5 text-xs text-cyan-100 backdrop-blur-sm focus:border-cyan-400 focus:outline-none focus:ring-1 focus:ring-cyan-400/50 transition-all">
                            <option value="dinamo_x" {{ $step['device'] === 'dinamo_x' ? 'selected' : '' }}>Dinamo X</option>
                            <option value="dinamo_y" {{ $step['device'] === 'dinamo_y' ? 'selected' : '' }}>Dinamo Y</option>
                            <option value="relay_pump" {{ $step['device'] === 'relay_pump' ? 'selected' : '' }}>Pompa Air</option>
                            <option value="servo_nozzle" {{ $step['device'] === 'servo_nozzle' ? 'selected' : '' }}>Servo Nozzle</option>
                        </select>

                        @if(in_array($step['device'], ['dinamo_x', 'dinamo_y']))
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" min="0" max="100" value="{{ $step['position'] ?? 50 }}" onchange="updateStepField({{ $index }}, 'position', this.value)" placeholder="Posisi %" class="w-full rounded border border-cyan-500/30 bg-slate-800/50 px-2 py-1 text-xs text-cyan-100 placeholder-cyan-400/30 backdrop-blur-sm focus:border-cyan-400 focus:outline-none focus:ring-1 focus:ring-cyan-400/50 transition-all">
                                <input type="number" min="0" max="255" value="{{ $step['speed'] ?? 128 }}" onchange="updateStepField({{ $index }}, 'speed', this.value)" placeholder="Speed" class="w-full rounded border border-cyan-500/30 bg-slate-800/50 px-2 py-1 text-xs text-cyan-100 placeholder-cyan-400/30 backdrop-blur-sm focus:border-cyan-400 focus:outline-none focus:ring-1 focus:ring-cyan-400/50 transition-all">
                            </div>
                        @endif
                    </div>

                    <!-- Connection handles -->
                    <div class="connection-handle absolute -right-4 top-1/2 -mt-3 h-6 w-6 rounded-full border-2 border-cyan-400 bg-slate-900 cursor-pointer transition-all opacity-0 group-hover:opacity-100 hover:scale-125 hover:shadow-[0_0_15px_rgba(34,211,238,0.8)]"
                         data-node="{{ $index }}"
                         data-side="right"
                         onclick="startConnection(event, {{ $index }}, 'right')"></div>
                    <div class="connection-handle absolute -left-4 top-1/2 -mt-3 h-6 w-6 rounded-full border-2 border-cyan-400 bg-slate-900 cursor-pointer transition-all opacity-0 group-hover:opacity-100 hover:scale-125 hover:shadow-[0_0_15px_rgba(34,211,238,0.8)]"
                         data-node="{{ $index }}"
                         data-side="left"
                         onclick="startConnection(event, {{ $index }}, 'left')"></div>
                    <div class="connection-handle absolute left-1/2 -bottom-4 -ml-3 h-6 w-6 rounded-full border-2 border-cyan-400 bg-slate-900 cursor-pointer transition-all opacity-0 group-hover:opacity-100 hover:scale-125 hover:shadow-[0_0_15px_rgba(34,211,238,0.8)]"
                         data-node="{{ $index }}"
                         data-side="bottom"
                         onclick="startConnection(event, {{ $index }}, 'bottom')"></div>
                    <div class="connection-handle absolute left-1/2 -top-4 -ml-3 h-6 w-6 rounded-full border-2 border-cyan-400 bg-slate-900 cursor-pointer transition-all opacity-0 group-hover:opacity-100 hover:scale-125 hover:shadow-[0_0_15px_rgba(34,211,238,0.8)]"
                         data-node="{{ $index }}"
                         data-side="top"
                         onclick="startConnection(event, {{ $index }}, 'top')"></div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<style>
    .flow-node {
        user-select: none;
        will-change: transform;
    }

    .node-header {
        cursor: grab;
    }

    .node-header:active {
        cursor: grabbing;
    }

    .connection-handle {
        z-index: 20;
        box-shadow: 0 0 10px rgba(6, 182, 212, 0.3);
    }

    .connection-handle.active {
        opacity: 1 !important;
        background-color: #06b6d4;
        box-shadow: 0 0 20px rgba(6, 182, 212, 0.8);
    }

    .connection-handle.connected {
        opacity: 1 !important;
        background: radial-gradient(circle at 30% 30%, rgba(6, 182, 212, 0.8), rgba(6, 182, 212, 0.4));
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.6);
        pointer-events: auto !important;
    }

    #flow-edges {
        will-change: contents;
    }

    @keyframes pulse-glow {
        0%, 100% { filter: drop-shadow(0 0 5px rgba(6, 182, 212, 0.4)); }
        50% { filter: drop-shadow(0 0 15px rgba(6, 182, 212, 0.8)); }
    }

    .edge-pulse {
        animation: pulse-glow 1.5s ease-in-out;
    }
</style>
