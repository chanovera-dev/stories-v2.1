function createSlideshow({
    wrapperSelector,
    slideshowSelector,
    navigationSelector,
    prevSelector = ".slide-prev",
    nextSelector = ".slide-next",
    bulletsSelector = ".related-bullets",
    autoTime = 10000,
    gap = 16,
    useBullets = true
}) {

    document.addEventListener("DOMContentLoaded", () => {

        const wrapper = document.querySelector(wrapperSelector)
        if (!wrapper) return

        const slideshow = wrapper.querySelector(slideshowSelector)
        const navigation = wrapper.querySelector(navigationSelector)
        const navPrev = wrapper.querySelector(prevSelector)
        const navNext = wrapper.querySelector(nextSelector)
        const bulletsContainer = wrapper.querySelector(bulletsSelector)

        if (!slideshow) return

        wrapper.style.overflow = "hidden"
        wrapper.style.display = "flex"
        wrapper.style.flexDirection = "column"
        wrapper.style.gap = "1rem"
        slideshow.style.display = "flex"
        navigation.style.display = "flex"
        navigation.style.alignItems = "center"
        navigation.style.justifyContent = "space-between"
        bulletsContainer.style.display = "flex"
        bulletsContainer.style.gap = "1rem"

        let slides = Array.from(slideshow.children)
        const totalOriginal = slides.length
        let itemsPerView = 1
        let slideWidth = 0
        let autoInterval = null
        let firstLoad = true

        // -------------------------------
        // BULLETS
        // -------------------------------
        function createBullets() {
            if (!useBullets || !bulletsContainer) return
            bulletsContainer.innerHTML = ""

            for (let i = 0; i < totalOriginal; i++) {
                const b = document.createElement("button")
                b.className = "bullet"
                b.dataset.id = slides[i].dataset.id
                bulletsContainer.appendChild(b)
            }
        }
        if (useBullets) createBullets()

        function updateBullets() {
            if (!useBullets || !bulletsContainer) return

            const bullets = bulletsContainer.querySelectorAll(".bullet")
            bullets.forEach(b => b.classList.remove("active"))

            const activeId = slides[0].dataset.id
            bullets.forEach(b => {
                if (b.dataset.id === activeId) b.classList.add("active")
            })
        }

        // -------------------------------
        // RESPONSIVE
        // -------------------------------
        function updateItemsPerView() {
            const w = window.innerWidth  // ancho de ventana

            if (w < 600) itemsPerView = 1
            else if (w < 809) itemsPerView = 2
            else if (w < 1065) itemsPerView = 3
            else itemsPerView = 4

            updateSlideWidth()
            updateBullets()
        }

        function updateSlideWidth() {
            const containerWidth = wrapper.getBoundingClientRect().width

            slideWidth =
                (containerWidth - (itemsPerView > 1 ? (itemsPerView - 1) * gap : 0)) /
                itemsPerView

            slideshow.style.gap = itemsPerView > 1 ? `${gap}px` : "0px"

            slides.forEach(s => {
                s.style.minWidth = slideWidth + "px"
                s.style.maxWidth = slideWidth + "px"
            })
        }

        // Ejecutar al cargar
        updateItemsPerView()

        // Escuchar cambios de tamaño de ventana
        window.addEventListener("resize", updateItemsPerView)

        // -------------------------------
        // ANIMACIONES
        // -------------------------------
        function updateAnimations() {
            if (firstLoad) return

            slides.forEach(s => s.classList.remove("animate-in"))

            for (let i = 0; i < itemsPerView; i++) {
                const visible = slides[i]
                if (visible) visible.classList.add("animate-in")
            }
        }

        // -------------------------------
        // NEXT
        // -------------------------------
        function next() {
            slideshow.style.transition = "all .5s ease-in-out"
            slideshow.style.transform = `translateX(-${slideWidth}px)`

            setTimeout(() => {
                slideshow.style.transition = "none"

                const first = slides[0]
                first.classList.remove("animate-in")

                slideshow.appendChild(first)

                slideshow.style.transform = `translateX(0)`

                slides = Array.from(slideshow.children)
                updateSlideWidth()
                updateBullets()

                // slides.forEach(s => s.classList.remove("animate-in"))
                void slideshow.offsetWidth

                for (let i = 0; i < Math.min(itemsPerView, slides.length); i++) {
                    slides[i].classList.add("animate-in")
                }

                requestAnimationFrame(() => {
                    slideshow.style.transition = "all .5s ease-in-out"
                })
            }, 500)
        }

        // -------------------------------
        // PREV
        // -------------------------------
        function prev() {
            slideshow.style.transition = "none"

            const last = slides[slides.length - 1]
            last.classList.remove("animate-in")

            slideshow.insertBefore(last, slides[0])

            slides = Array.from(slideshow.children)

            slideshow.style.transform = `translateX(-${slideWidth}px)`

            requestAnimationFrame(() => {
                slideshow.style.transition = "all .5s ease-in-out"
                slideshow.style.transform = `translateX(0)`

                setTimeout(() => last.classList.add("animate-in"), 500)
            })

            updateSlideWidth()
            updateBullets()
            updateAnimations()
        }

        if (navNext) navNext.addEventListener("click", next)
        if (navPrev) navPrev.addEventListener("click", prev)

        // -------------------------------
        // BULLETS → SALTO
        // -------------------------------
        let bulletJumping = false

        function goToSlideById(targetId) {
            if (!useBullets) return

            if (bulletJumping) return
            bulletJumping = true

            function step() {
                if (slides[0].dataset.id === targetId) {
                    bulletJumping = false
                    return
                }
                next()
                setTimeout(step, 520)
            }

            step()
        }

        if (useBullets && bulletsContainer) {
            bulletsContainer.querySelectorAll(".bullet").forEach(b =>
                b.addEventListener("click", () => goToSlideById(b.dataset.id))
            )
        }

        // -------------------------------
        // AUTO SLIDE
        // -------------------------------
        function startAuto() {
            stopAuto()
            autoInterval = setInterval(next, autoTime)
        }
        function stopAuto() {
            if (autoInterval) clearInterval(autoInterval)
        }

        wrapper.addEventListener("mouseenter", stopAuto)
        wrapper.addEventListener("mouseleave", startAuto)

        // -------------------------------
        // TOUCH
        // -------------------------------
        let touchStartX = 0
        let touchEndX = 0
        const SWIPE_THRESHOLD = 50

        wrapper.addEventListener("touchstart", (e) => {
            stopAuto()
            touchStartX = e.touches[0].clientX
            touchEndX = touchStartX
        }, { passive: true })

        wrapper.addEventListener("touchmove", (e) => {
            touchEndX = e.touches[0].clientX
        }, { passive: true })

        wrapper.addEventListener("touchend", () => {
            const dx = touchEndX - touchStartX

            if (Math.abs(dx) > SWIPE_THRESHOLD) {
                if (dx < 0) next()
                else prev()
            }
            startAuto()
        })

        // -------------------------------
        // INIT
        // -------------------------------
        updateItemsPerView()

        requestAnimationFrame(() => {
            updateBullets()
            startAuto()
        })
    })
}

createSlideshow({
    wrapperSelector: ".slideshow-wrapper",
    slideshowSelector: ".slideshow",
    navigationSelector: ".navigation",
}) 