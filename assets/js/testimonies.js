document.addEventListener("DOMContentLoaded", function () {
    const wrappers = document.querySelectorAll(".testimonies-wrapper")

    wrappers.forEach((wrapper) => {
        const navigation = wrapper.querySelector(".navigation")
        const testimonies = wrapper.querySelector(".testimonies")
        const originalSlides = Array.from(wrapper.querySelectorAll(".testimonies > *"))
        const bulletsWrapper = wrapper.querySelector(".testimonies-bullets")

        if (!testimonies || originalSlides.length === 0 || !bulletsWrapper) return

        // Clonar para efecto infinito
        const firstClone = originalSlides[0].cloneNode(true)
        const lastClone = originalSlides[originalSlides.length - 1].cloneNode(true)
        testimonies.prepend(lastClone)
        testimonies.appendChild(firstClone)

        const slides = testimonies.querySelectorAll(".testimonies > *")
        const totalSlides = slides.length
        const visibleSlides = originalSlides.length

        let currentSlide = 1 // Empieza en 1 (primer slide real)
        let animationFrame
        let isAnimating = false

        // Ajustar anchos
        testimonies.style.width = `${100 * totalSlides}%`
        slides.forEach(slide => {
            slide.style.width = `${100 / totalSlides}%`
            slide.style.transition = "opacity .3s ease-in-out, transform .3s ease-in-out"
            slide.style.opacity = "0"
            slide.style.transform = "translateY(60px)"
        })
        testimonies.style.display = "flex"
        testimonies.style.alignItems = "center"

        // Posicionar en el primer slide real
        testimonies.style.transform = `translateX(-${(100 / totalSlides) * currentSlide}%)`

        navigation.style.display = "flex"
        navigation.style.justifyContent = "space-between"
        navigation.style.alignItems = "center"
        navigation.style.marginTop = "2rem"

        // Crear botones
        bulletsWrapper.innerHTML = ""
        originalSlides.forEach((_, index) => {
            const bullet = document.createElement("div")
            bullet.classList.add("bullet")
            if (index === 0) bullet.classList.add("active")
            bullet.dataset.index = index
            bulletsWrapper.appendChild(bullet)
        })

        bulletsWrapper.style.display = "flex"
        bulletsWrapper.style.justifyContent = "center"
        bulletsWrapper.style.alignItems = "center"
        bulletsWrapper.style.gap = "20px"

        const bullets = bulletsWrapper.querySelectorAll(".bullet")

        function updateActiveClasses() {
            slides.forEach(slide => {
                if (slide.classList.contains("active")) {
                    slide.style.opacity = "0"
                    slide.style.transform = "translateY(60px)"
                }
                slide.classList.remove("active")
            })

            if (currentSlide >= 0 && currentSlide < totalSlides) {
                const activeSlide = slides[currentSlide]
                activeSlide.classList.add("active")
                activeSlide.style.opacity = "1"
                activeSlide.style.transform = "translateY(0)"
            }

            const realIndex = (currentSlide - 1 + visibleSlides) % visibleSlides
            bullets.forEach((btn, i) => {
                btn.classList.toggle("active", i === realIndex)
            })
        }

        function goToSlide(targetIndex) {
            if (isAnimating) return
            isAnimating = true

            // Animación de salida del slide actual
            const currentActive = slides[currentSlide]
            currentActive.style.opacity = "0"
            currentActive.style.transform = "translateY(60px)"

            setTimeout(() => {
                const from = (100 / totalSlides) * currentSlide
                const to = (100 / totalSlides) * targetIndex
                const distance = to - from
                const duration = 300

                const startTime = performance.now()

                function animate(time) {
                    const elapsed = time - startTime
                    const progress = Math.min(elapsed / duration, 1)
                    const current = from + distance * progress
                    testimonies.style.transform = `translateX(-${current}%)`

                    if (progress < 1) {
                        animationFrame = requestAnimationFrame(animate)
                    } else {
                        cancelAnimationFrame(animationFrame)
                        currentSlide = targetIndex

                        if (currentSlide === 0) {
                            currentSlide = visibleSlides
                            testimonies.style.transition = "none"
                            testimonies.style.transform = `translateX(-${(100 / totalSlides) * currentSlide}%)`
                            requestAnimationFrame(() => {
                                testimonies.style.transition = "ease"
                                finalize()
                            })
                        } else if (currentSlide === totalSlides - 1) {
                            currentSlide = 1
                            testimonies.style.transition = "none"
                            testimonies.style.transform = `translateX(-${(100 / totalSlides) * currentSlide}%)`
                            requestAnimationFrame(() => {
                                testimonies.style.transition = "ease"
                                finalize()
                            })
                        } else {
                            finalize()
                        }
                    }
                }

                function finalize() {
                    updateActiveClasses()
                    isAnimating = false
                }

                cancelAnimationFrame(animationFrame)
                animationFrame = requestAnimationFrame(animate)
            }, 300)
        }

        bulletsWrapper.addEventListener("click", function (e) {
            if (e.target.classList.contains("bullet")) {
                const index = parseInt(e.target.dataset.index)
                goToSlide(index + 1) // +1 por el clon al inicio
            }
        })

        // Swipe
        let startX = 0
        let endX = 0
        const threshold = 50

        testimonies.addEventListener("touchstart", function (e) {
            startX = e.touches[0].clientX
        })

        testimonies.addEventListener("touchmove", function (e) {
            endX = e.touches[0].clientX
        })

        testimonies.addEventListener("touchend", function () {
            const deltaX = endX - startX
            if (Math.abs(deltaX) > threshold) {
                if (deltaX < 0) {
                    goToSlide(currentSlide + 1)
                } else {
                    goToSlide(currentSlide - 1)
                }
            }
            startX = 0
            endX = 0
        })

        // Botones prev/next
        const prevBtn = wrapper.querySelector(".testimonies-prev")
        const nextBtn = wrapper.querySelector(".testimonies-next")

        if (prevBtn) {
            prevBtn.addEventListener("click", () => {
                goToSlide(currentSlide - 1)
            })
        }

        if (nextBtn) {
            nextBtn.addEventListener("click", () => {
                goToSlide(currentSlide + 1)
            })
        }

        // Inicializar con un pequeño delay para permitir la animación inicial
        setTimeout(() => {
            updateActiveClasses()
        }, 500)

        // Autoslide cada 10 segundos
        let autoSlide = setInterval(() => {
            goToSlide(currentSlide + 1)
        }, 10000)

        // Opcional: reiniciar el intervalo si el usuario interactúa
        function resetAutoSlide() {
            clearInterval(autoSlide)
            autoSlide = setInterval(() => {
                goToSlide(currentSlide + 1)
            }, 10000)
        }

        // Reiniciar cuando se hace clic manualmente
        bulletsWrapper.addEventListener("click", function (e) {
            if (e.target.classList.contains("bullet")) {
                const index = parseInt(e.target.dataset.index)
                goToSlide(index + 1) // +1 por el clon al inicio
                resetAutoSlide()
            }
        })

        // Reiniciar al usar botones prev/next
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

    })
})