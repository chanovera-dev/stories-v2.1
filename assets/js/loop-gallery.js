const initializedGalleries = new WeakSet()

function initGallery(wrapper) {
    if (initializedGalleries.has(wrapper)) return
    initializedGalleries.add(wrapper)

    const gallery = wrapper.querySelector(".gallery")
    const originalSlides = Array.from(wrapper.querySelectorAll(".gallery > *:not(.is-clone)"))
    // Remove any existing clones (e.g., from a cloneNode operation)
    wrapper.querySelectorAll(".gallery > .is-clone").forEach(el => el.remove())
    const navigation = wrapper.querySelector(".gallery-navigation")
    const bulletsWrapper = wrapper.querySelector(".loop-gallery-bullets")

    if (!gallery || originalSlides.length === 0 || !bulletsWrapper) return

    wrapper.style.height = "calc(100% - .5rem)"
    wrapper.style.overflow = "hidden"
    wrapper.style.display = "grid"
    wrapper.style.gridTemplateRows = "1fr auto"
    gallery.style.display = "flex"
    gallery.style.height = "100%"

    const firstClone = originalSlides[0].cloneNode(true)
    const lastClone = originalSlides[originalSlides.length - 1].cloneNode(true)
    firstClone.classList.add("is-clone")
    lastClone.classList.add("is-clone")
    gallery.prepend(lastClone)
    gallery.appendChild(firstClone)

    const slides = gallery.querySelectorAll(".gallery > *")
    const totalSlides = slides.length
    const visibleSlides = originalSlides.length

    let currentSlide = 1
    let animationFrame
    let isAnimating = false

    gallery.style.width = `${100 * totalSlides}%`
    slides.forEach(slide => {
        slide.style.width = `${100 / totalSlides}%`
        slide.style.transition = "transform 0.5s ease, opacity 0.5s ease"
        slide.style.transform = "scale(1)"
        slide.style.opacity = "0.75"
        slide.style.position = "relative"
    })

    gallery.style.transform = `translateX(-${(100 / totalSlides) * currentSlide}%)`

    navigation.style.display = "flex"
    navigation.style.justifyContent = "space-between"
    navigation.style.alignItems = "center"
    navigation.style.padding = ".5rem"

    bulletsWrapper.innerHTML = ""
    const bigGalleryIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-images" viewBox="0 0 16 16"><path d="M4.502 9a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/><path d="M14.002 13a2 2 0 0 1-2 2h-10a2 2 0 0 1-2-2V5A2 2 0 0 1 2 3a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v8a2 2 0 0 1-1.998 2M14 2H4a1 1 0 0 0-1 1h9.002a2 2 0 0 1 2 2v7A1 1 0 0 0 15 11V3a1 1 0 0 0-1-1M2.002 4a1 1 0 0 0-1 1v8l2.646-2.354a.5.5 0 0 1 .63-.062l2.66 1.773 3.71-3.71a.5.5 0 0 1 .577-.094l1.777 1.947V5a1 1 0 0 0-1-1z"/></svg>`
    if (originalSlides.length > 5) {
        bulletsWrapper.style.display = "flex"
        bulletsWrapper.style.gap = "0.5rem"
        bulletsWrapper.style.alignItems = "center"
        bulletsWrapper.innerHTML = `${bigGalleryIcon} ${originalSlides.length}`

    } else {
        bulletsWrapper.style.display = "flex"
        bulletsWrapper.style.gap = "1rem"
        bulletsWrapper.style.alignItems = "center"
        originalSlides.forEach((_, index) => {
            const bullet = document.createElement("div")
            bullet.classList.add("bullet")
            if (index === 0) bullet.classList.add("active")
            bullet.dataset.index = index
            bulletsWrapper.appendChild(bullet)
        })
    }

    const bullets = bulletsWrapper.querySelectorAll(".bullet")

    function updateActiveClasses(index = currentSlide, shouldGrow = true) {
        slides.forEach(slide => {
            slide.classList.remove("active")
            slide.style.transform = "scale(1)"
            slide.style.opacity = "0.75"
        })

        if (shouldGrow && slides[index]) {
            slides[index].classList.add("active")
            slides[index].style.transform = "scale(1)"
            slides[index].style.opacity = "1"
        }

        const realIndex = ((index - 1) % visibleSlides + visibleSlides) % visibleSlides
        bullets.forEach((btn, i) => btn.classList.toggle("active", i === realIndex))
    }

    function handleInfiniteLoop() {
        if (currentSlide === 0) {
            currentSlide = visibleSlides
        } else if (currentSlide === totalSlides - 1) {
            currentSlide = 1
        } else {
            return false
        }

        gallery.style.transition = "none"
        slides.forEach(s => s.style.transition = "none")
        gallery.style.transform = `translateX(-${(100 / totalSlides) * currentSlide}%)`

        requestAnimationFrame(() => {
            gallery.style.transition = ""
            slides.forEach(s => s.style.transition = "transform 0.5s ease, opacity 0.5s ease")
            updateActiveClasses()
            isAnimating = false
        })

        return true
    }

    function goToSlide(targetIndex) {
        if (isAnimating) return
        isAnimating = true

        updateActiveClasses(targetIndex, false)

        setTimeout(() => {
            const from = (100 / totalSlides) * currentSlide
            const to = (100 / totalSlides) * targetIndex
            const distance = to - from
            const duration = 400
            const startTime = performance.now()

            function animate(time) {
                const elapsed = time - startTime
                const progress = Math.min(elapsed / duration, 1)
                const current = from + distance * progress
                gallery.style.transform = `translateX(-${current}%)`

                if (progress < 1) {
                    animationFrame = requestAnimationFrame(animate)
                } else {
                    cancelAnimationFrame(animationFrame)
                    currentSlide = targetIndex

                    if (!handleInfiniteLoop()) {
                        updateActiveClasses()
                        isAnimating = false
                    }
                }
            }

            cancelAnimationFrame(animationFrame)
            animationFrame = requestAnimationFrame(animate)
        }, 500)
    }

    bulletsWrapper.addEventListener("click", e => {
        if (e.target.classList.contains("bullet")) {
            const index = parseInt(e.target.dataset.index, 10)
            goToSlide(index + 1)
            resetAutoSlide()
        }
    })

    let startX = 0
    let endX = 0
    const threshold = 50

    gallery.addEventListener("touchstart", e => startX = e.touches[0].clientX, { passive: true })
    gallery.addEventListener("touchmove", e => endX = e.touches[0].clientX, { passive: true })
    gallery.addEventListener("touchend", () => {
        const deltaX = endX - startX
        if (Math.abs(deltaX) > threshold) {
            goToSlide(deltaX < 0 ? currentSlide + 1 : currentSlide - 1)
        }
        startX = 0
        endX = 0
    })

    const prevBtn = wrapper.querySelector(".gallery-prev")
    const nextBtn = wrapper.querySelector(".gallery-next")

    if (prevBtn) {
        prevBtn.addEventListener("click", () => {
            goToSlide(currentSlide - 1)
            resetAutoSlide()
        })
    }

    if (nextBtn) {
        nextBtn.addEventListener("click", () => {
            goToSlide(currentSlide + 1)
            resetAutoSlide()
        })
    }

    updateActiveClasses()

    // Autoplay disabled - gallery only moves manually
    // let autoSlide = setInterval(() => goToSlide(currentSlide + 1), 14000)

    function resetAutoSlide() {
        // clearInterval(autoSlide)
        // autoSlide = setInterval(() => goToSlide(currentSlide + 1), 10000)
    }

    // wrapper.addEventListener("mouseenter", () => clearInterval(autoSlide))
    // wrapper.addEventListener("mouseleave", resetAutoSlide)
}

function initAllGalleries() {
    document.querySelectorAll(".gallery-wrapper").forEach(initGallery)
}

const observer = new MutationObserver(() => initAllGalleries())
observer.observe(document.body, { childList: true, subtree: true })

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAllGalleries)
} else {
    initAllGalleries()
}