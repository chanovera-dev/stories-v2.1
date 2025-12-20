document.addEventListener("DOMContentLoaded", function () {
    const wrappers = document.querySelectorAll(".container")

    wrappers.forEach((wrapper) => {
        const slideshow = wrapper.querySelector(".slideshow")
        const originalSlides = Array.from(wrapper.querySelectorAll(".slideshow > *"))
        const bulletsWrapper = wrapper.querySelector(".slideshow-bullets")

        if (!slideshow || originalSlides.length === 0 || !bulletsWrapper) return

        const firstClone = originalSlides[0].cloneNode(true)
        const lastClone = originalSlides[originalSlides.length - 1].cloneNode(true)
        slideshow.prepend(lastClone)
        slideshow.appendChild(firstClone)

        const slides = slideshow.querySelectorAll(".slideshow > *")
        const totalSlides = slides.length
        const visibleSlides = originalSlides.length

        let currentSlide = 1
        let animationFrame
        let isAnimating = false

        slideshow.style.width = `${100 * totalSlides}%`
        slides.forEach(slide => {
            slide.style.width = `${100 / totalSlides}%`
        })

        slideshow.style.transform = `translateX(-${(100 / totalSlides) * currentSlide}%)`

        bulletsWrapper.innerHTML = ""
        originalSlides.forEach((_, index) => {
            const bullet = document.createElement("div")
            bullet.classList.add("bullet")
            if (index === 0) bullet.classList.add("active")
            bullet.dataset.index = index
            bulletsWrapper.appendChild(bullet)
        })

        const bullets = bulletsWrapper.querySelectorAll(".bullet")

        function updateActiveClasses() {
            originalSlides.forEach(slide => slide.classList.remove("active"))

            const realIndex = currentSlide - 1
            if (realIndex >= 0 && realIndex < visibleSlides) {
                originalSlides[realIndex].classList.add("active")
            }

            bullets.forEach((btn, i) => {
                btn.classList.toggle("active", i === realIndex)
            })
        }

        function goToSlide(targetIndex) {
            if (isAnimating) return
            isAnimating = true

            const from = (100 / totalSlides) * currentSlide
            const to = (100 / totalSlides) * targetIndex
            const distance = to - from
            const duration = 400

            const startTime = performance.now()

            function animate(time) {
                const elapsed = time - startTime
                const progress = Math.min(elapsed / duration, 1)
                const current = from + distance * progress
                slideshow.style.transform = `translateX(-${current}%)`

                if (progress < 1) {
                    animationFrame = requestAnimationFrame(animate)
                } else {
                    cancelAnimationFrame(animationFrame)
                    currentSlide = targetIndex

                    if (currentSlide === 0) {
                        currentSlide = visibleSlides
                        slideshow.style.transition = "none"
                        slideshow.style.transform = `translateX(-${(100 / totalSlides) * currentSlide}%)`
                        requestAnimationFrame(() => {
                            slideshow.style.transition = ""
                        })
                    } else if (currentSlide === totalSlides - 1) {
                        currentSlide = 1
                        slideshow.style.transition = "none"
                        slideshow.style.transform = `translateX(-${(100 / totalSlides) * currentSlide}%)`
                        requestAnimationFrame(() => {
                            slideshow.style.transition = ""
                        })
                    }

                    updateActiveClasses()
                    isAnimating = false
                }
            }

            cancelAnimationFrame(animationFrame)
            animationFrame = requestAnimationFrame(animate)
        }

        bulletsWrapper.addEventListener("click", function (e) {
            if (e.target.classList.contains("slideshow-bullet")) {
                const index = parseInt(e.target.dataset.index)
                goToSlide(index + 1)
            }
        })

        let startX = 0
        let endX = 0
        const threshold = 50

        slideshow.addEventListener("touchstart", function (e) {
            startX = e.touches[0].clientX
        })

        slideshow.addEventListener("touchmove", function (e) {
            endX = e.touches[0].clientX
        })

        slideshow.addEventListener("touchend", function () {
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

        const prevBtn = wrapper.querySelector(".slideshow-prev")
        const nextBtn = wrapper.querySelector(".slideshow-next")

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

        updateActiveClasses()

        let autoSlide = setInterval(() => {
            goToSlide(currentSlide + 1)
        }, 14000)

        function resetAutoSlide() {
            clearInterval(autoSlide)
            autoSlide = setInterval(() => {
                goToSlide(currentSlide + 1)
            }, 14000)
        }

        bulletsWrapper.addEventListener("click", function (e) {
            if (e.target.classList.contains("slideshow-bullet")) {
                const index = parseInt(e.target.dataset.index)
                goToSlide(index + 1)
                resetAutoSlide()
            }
        })

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