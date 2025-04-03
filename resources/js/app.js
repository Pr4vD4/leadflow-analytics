import './bootstrap';
import Alpine from 'alpinejs';
import AOS from 'aos';
import { tsParticles } from "tsparticles-engine";
import { loadSlim } from "tsparticles-slim";
import { gsap } from "gsap";

// Делаем GSAP доступным глобально
window.gsap = gsap;

// Инициализация tsParticles
window.initParticles = async function(containerId) {
    await loadSlim(tsParticles);

    await tsParticles.load(containerId, {
        fullScreen: {
            enable: false
        },
        fpsLimit: 120,
        particles: {
            number: {
                value: 30,
                density: {
                    enable: true,
                    value_area: 800
                }
            },
            color: {
                value: ["#4F46E5", "#6366F1", "#0F172A", "#334155"],
            },
            shape: {
                type: "circle",
            },
            opacity: {
                value: 0.5,
                random: true,
                anim: {
                    enable: true,
                    speed: 1,
                    opacity_min: 0.1,
                    sync: false
                }
            },
            size: {
                value: 6,
                random: true,
                anim: {
                    enable: true,
                    speed: 1,
                    size_min: 2,
                    sync: false
                }
            },
            line_linked: {
                enable: true,
                distance: 150,
                color: {
                    value: "#4F46E5"
                },
                opacity: 0.2,
                width: 1
            },
            move: {
                enable: true,
                speed: 1,
                direction: "none",
                random: true,
                straight: false,
                out_mode: "bounce",
                bounce: false,
                attract: {
                    enable: true,
                    rotateX: 600,
                    rotateY: 1200
                }
            }
        },
        interactivity: {
            detectsOn: "canvas",
            events: {
                onHover: {
                    enable: true,
                    mode: "grab"
                },
                onClick: {
                    enable: true,
                    mode: "push"
                },
                resize: true
            },
            modes: {
                grab: {
                    distance: 140,
                    line_linked: {
                        opacity: 0.6
                    }
                },
                push: {
                    particles_nb: 4
                }
            }
        },
        detectRetina: true,
        pauseOnBlur: false,
        themes: [
            {
                name: "light",
                default: {
                    value: true,
                    mode: "light"
                },
                options: {
                    background: {
                        color: "#f9fafb"
                    },
                    particles: {
                        color: {
                            value: ["#4F46E5", "#6366F1", "#0F172A", "#334155"]
                        },
                        line_linked: {
                            color: "#4F46E5"
                        }
                    }
                }
            },
            {
                name: "dark",
                default: {
                    value: false,
                    mode: "dark"
                },
                options: {
                    background: {
                        color: "#0f172a"
                    },
                    particles: {
                        color: {
                            value: ["#4F46E5", "#6366F1", "#e2e8f0", "#94a3b8"]
                        },
                        line_linked: {
                            color: "#4F46E5"
                        }
                    }
                }
            }
        ]
    });
};

// Инициализация Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Инициализация AOS
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        mirror: false
    });
});
