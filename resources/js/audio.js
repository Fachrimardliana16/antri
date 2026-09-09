// Web Audio API Synthesized Chime & Web Speech API Engine
class AntriAudioEngine {
    constructor() {
        this.audioCtx = null;
        this.synth = window.speechSynthesis;
        this.isSpeaking = false;
        this.voice = null;
        this.initVoice();
    }

    getAudioContext() {
        if (!this.audioCtx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                this.audioCtx = new AudioContext();
            }
        }
        if (this.audioCtx && this.audioCtx.state === 'suspended') {
            this.audioCtx.resume();
        }
        return this.audioCtx;
    }

    initVoice() {
        if (!this.synth) return;
        const setVoice = () => {
            const voices = this.synth.getVoices();
            // Prefer Indonesian voice
            this.voice = voices.find(v => v.lang.startsWith('id') || v.lang === 'id_ID' || v.name.toLowerCase().includes('indonesia')) ||
                         voices.find(v => v.lang.startsWith('en')) ||
                         voices[0];
        };
        setVoice();
        if (this.synth.onvoiceschanged !== undefined) {
            this.synth.onvoiceschanged = setVoice;
        }
    }

    // Play dual-tone pleasant Ding-Dong chime
    playChime() {
        return new Promise((resolve) => {
            try {
                const ctx = this.getAudioContext();
                if (!ctx) return resolve();

                const now = ctx.currentTime;
                
                // Tone 1: High (G5 - 784Hz or E5 - 659.25Hz)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(659.25, now);
                gain1.gain.setValueAtTime(0.3, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.6);

                // Tone 2: Low (C5 - 523.25Hz) after 300ms
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(523.25, now + 0.35);
                gain2.gain.setValueAtTime(0.35, now + 0.35);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 1.1);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.35);
                osc2.stop(now + 1.1);

                setTimeout(resolve, 1100);
            } catch (e) {
                console.warn('Audio chime error:', e);
                resolve();
            }
        });
    }

    // Speak text with audio ducking
    async speak(text, rate = 0.9, pitch = 1.0, onStart = null, onEnd = null) {
        if (!this.synth) return;

        // Cancel previous pending utterances
        this.synth.cancel();

        // 1. Duck audio if callback provided
        if (onStart) onStart();

        // 2. Play chime first
        await this.playChime();

        // 3. Articulate text
        return new Promise((resolve) => {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = parseFloat(rate) || 0.9;
            utterance.pitch = parseFloat(pitch) || 1.0;
            
            if (this.voice) {
                utterance.voice = this.voice;
            }

            utterance.onend = () => {
                this.isSpeaking = false;
                if (onEnd) onEnd();
                resolve();
            };

            utterance.onerror = (err) => {
                console.warn('TTS error:', err);
                this.isSpeaking = false;
                if (onEnd) onEnd();
                resolve();
            };

            this.isSpeaking = true;
            this.synth.speak(utterance);
        });
    }
}

window.AntriAudio = new AntriAudioEngine();
