function animateIn(selectors, animationClasses = ['animate-in'], options = {}) {
    const {
        threshold = 0.1,
        stagger = 200
    } = options;

    const targets = document.querySelectorAll(selectors);
    if (!targets.length) return;

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting && entry.intersectionRatio >= threshold) {
                setTimeout(() => {
                    animationClasses.forEach(cls => entry.target.classList.add(cls));
                    observer.unobserve(entry.target);
                }, index * stagger);
            }
        });
    }, { threshold });

    targets.forEach(target => observer.observe(target));
}