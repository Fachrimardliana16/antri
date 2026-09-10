<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Display Antrian</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600,700,800,900" rel="stylesheet" />

    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full w-full overflow-hidden">
    {{ $slot ?? '' }}
    @yield('content')

    @livewireScripts

    <script>
        // Audio system for TV display
        window.AntriAudio = {
            audioContext: null,
            selectedVoice: null,
            voices: [],

            getAudioContext() {
                if (!this.audioContext) {
                    this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                }
                return this.audioContext;
            },

            loadVoices() {
                return new Promise((resolve) => {
                    let voices = window.speechSynthesis.getVoices();
                    if (voices.length) {
                        this.voices = voices;
                        this.selectBestVoice();
                        resolve(voices);
                    } else {
                        window.speechSynthesis.onvoiceschanged = () => {
                            this.voices = window.speechSynthesis.getVoices();
                            this.selectBestVoice();
                            resolve(this.voices);
                        };
                    }
                });
            },

            selectBestVoice() {
                // Priority: Indonesian voices, then default
                const preferences = [
                    'id-ID', // Indonesian
                    'id_ID',
                    'Indonesian'
                ];

                for (let pref of preferences) {
                    const voice = this.voices.find(v =>
                        v.lang.includes(pref) || v.name.includes(pref)
                    );
                    if (voice) {
                        this.selectedVoice = voice;
                        console.log('Selected voice:', voice.name, voice.lang);
                        return;
                    }
                }

                // Fallback to first available voice
                this.selectedVoice = this.voices[0];
                console.log('Fallback voice:', this.selectedVoice?.name);
            },

            listAvailableVoices() {
                console.log('Available voices:');
                this.voices.forEach((voice, i) => {
                    console.log(`${i}: ${voice.name} (${voice.lang}) ${voice.default ? '[DEFAULT]' : ''}`);
                });
            },

            setVoice(voiceIndex) {
                if (this.voices[voiceIndex]) {
                    this.selectedVoice = this.voices[voiceIndex];
                    console.log('Voice changed to:', this.selectedVoice.name);
                }
            },

            speak(text, volume = 0.9, rate = 1.0, onStart = null, onEnd = null) {
                if (!window.speechSynthesis) return;

                // Load voices if not loaded yet
                if (!this.voices.length) {
                    this.loadVoices().then(() => {
                        this.speak(text, volume, rate, onStart, onEnd);
                    });
                    return;
                }

                const utterance = new SpeechSynthesisUtterance(text);

                // Use selected voice
                if (this.selectedVoice) {
                    utterance.voice = this.selectedVoice;
                }

                utterance.lang = 'id-ID';
                utterance.volume = volume;
                utterance.rate = rate;
                utterance.pitch = 1.0;

                utterance.onstart = () => {
                    if (onStart) onStart();
                };

                utterance.onend = () => {
                    if (onEnd) onEnd();
                };

                window.speechSynthesis.speak(utterance);
            }
        };

        // Load voices on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                window.AntriAudio.loadVoices();
            });
        } else {
            window.AntriAudio.loadVoices();
        }

        // Debug helper: list voices in console
        // Call: AntriAudio.listAvailableVoices()
        // Change voice: AntriAudio.setVoice(indexNumber)
    </script>
</body>
</html>
