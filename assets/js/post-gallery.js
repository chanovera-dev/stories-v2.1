function postGalleries() {
    const initialized = new WeakSet()

    function postGallery(wrapper) {
        if (initialized.has(wrapper)) return
        initialized.add(wrapper)

        const gallery = wrapper.querySelector(".post-gallery")
        const slides = Array.from(gallery.querySelectorAll(".post-gallery-slide"))
        const thumbsWrapper = wrapper.querySelector(".post-gallery-thumbs")
        const prevBtn = wrapper.querySelector(".btn-pagination:first-of-type")
        const nextBtn = wrapper.querySelector(".btn-pagination:last-of-type")

        if (!slides.length || !thumbsWrapper) return

        const firstClone = slides[0].cloneNode(true)
        const lastClone = slides[slides.length - 1].cloneNode(true)
        gallery.prepend(lastClone)
        gallery.append(firstClone)

        const totalImages = wrapper.querySelector(".total-images")
        const bigGalleryIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-images" viewBox="0 0 16 16"><path d="M4.502 9a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/><path d="M14.002 13a2 2 0 0 1-2 2h-10a2 2 0 0 1-2-2V5A2 2 0 0 1 2 3a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v8a2 2 0 0 1-1.998 2M14 2H4a1 1 0 0 0-1 1h9.002a2 2 0 0 1 2 2v7A1 1 0 0 0 15 11V3a1 1 0 0 0-1-1M2.002 4a1 1 0 0 0-1 1v8l2.646-2.354a.5.5 0 0 1 .63-.062l2.66 1.773 3.71-3.71a.5.5 0 0 1 .577-.094l1.777 1.947V5a1 1 0 0 0-1-1z"/></svg>`

        const allSlides = Array.from(gallery.querySelectorAll(".post-gallery-slide"))
        const totalSlides = allSlides.length
        const originalCount = slides.length

        let currentSlide = 1
        let isAnimating = false

        gallery.style.display = "flex"
        gallery.style.width = `${100 * totalSlides}%`

        allSlides.forEach(s => {
            s.style.width = `${100 / totalSlides}%`
            s.style.transform = "scale(0.5)"
            s.style.opacity = "0.5"
            s.style.transition = "transform .5s ease, opacity .5s ease"
        })

        gallery.style.transform = `translateX(-${(100 / totalSlides) * currentSlide}%)`
        totalImages.innerHTML = `${bigGalleryIcon} ${slides.length}`

        let startX = 0
        let endX = 0
        let touchLocked = false

        gallery.addEventListener("touchstart", e => {
            if (isAnimating) return
            startX = e.touches[0].clientX
            touchLocked = false
        }, { passive: true })

        gallery.addEventListener("touchmove", e => {
            if (isAnimating || touchLocked) return
            endX = e.touches[0].clientX
            const diff = endX - startX

            if (Math.abs(diff) > 40) {
                touchLocked = true
                if (diff < 0) {
                    goToSlide(currentSlide + 1)
                } else {
                    goToSlide(currentSlide - 1)
                }
                resetAuto()
            }
        }, { passive: true })

        gallery.addEventListener("touchend", () => {
            startX = 0
            endX = 0
            touchLocked = false
        })

        thumbsWrapper.innerHTML = ""
        slides.forEach((slide, index) => {
            const t = slide.cloneNode(true)
            t.removeAttribute("style")
            t.className = "post-gallery-thumb"
            t.dataset.index = index
            if (index === 0) t.classList.add("active")
            thumbsWrapper.append(t)
        })

        let originalThumbs = Array.from(thumbsWrapper.querySelectorAll(".post-gallery-thumb"))
        const ORIGINAL_COUNT = originalThumbs.length
        const VISIBLE = 3
        const GAP = 1

        thumbsWrapper.style.display = "flex"
        thumbsWrapper.style.gap = `${GAP}px`
        thumbsWrapper.style.overflow = "hidden"

        function buildThumbClones() {
            const pre = []
            originalThumbs.forEach(t => pre.push(t.cloneNode(true)))
            originalThumbs.forEach(t => pre.push(t.cloneNode(true)))
            pre.reverse().forEach(c => thumbsWrapper.prepend(c))
            originalThumbs.forEach(t => thumbsWrapper.append(t.cloneNode(true)))
            originalThumbs.forEach(t => thumbsWrapper.append(t.cloneNode(true)))
        }
        buildThumbClones()

        let allThumbs = Array.from(thumbsWrapper.querySelectorAll(".post-gallery-thumb"))

        function updateThumbWrapperWidth() {
            const size = window.innerWidth < 600 ? 46 : 100
            const totalGaps = GAP * (VISIBLE - 1)
            thumbsWrapper.style.width = ((size + 7) * VISIBLE + totalGaps) + "px"
        }
        updateThumbWrapperWidth()

        function updateThumbImageSize() {
            const size = window.innerWidth < 600 ? 46 : 100
            allThumbs.forEach(t => {
                const img = t.querySelector("img")
                if (img) {
                    img.style.width = size + "px"
                    img.style.height = "calc(46px - .4rem)"
                    img.style.objectFit = "cover"
                }
            })
        }
        updateThumbImageSize()

        function updateThumbWidths() {
            const containerWidth = thumbsWrapper.clientWidth
            const totalGaps = GAP * (VISIBLE - 1)
            const itemWidth = Math.floor((containerWidth - totalGaps) / VISIBLE)
            allThumbs.forEach(t => {
                t.style.flex = `0 0 ${itemWidth}px`
                t.style.width = `${itemWidth}px`
                t.style.height = "auto"
                t.style.padding = ".2rem"
            })
        }
        updateThumbWidths()

        window.addEventListener("resize", () => {
            updateThumbWrapperWidth()
            updateThumbImageSize()
            updateThumbWidths()
            requestAnimationFrame(centerActiveThumb)
        })

        function initialThumbScrollPosition() {
            const w = allThumbs[0].getBoundingClientRect().width + GAP
            const clonesBefore = ORIGINAL_COUNT * 2
            thumbsWrapper.scrollLeft = w * clonesBefore
        }
        initialThumbScrollPosition()

        function realIndex() {
            return (currentSlide - 1 + ORIGINAL_COUNT) % ORIGINAL_COUNT
        }

        function centerActiveThumb() {
            const idx = realIndex()
            const targets = allThumbs.filter(t => t.dataset.index == idx)
            if (!targets.length) return
            const wrapperRect = thumbsWrapper.getBoundingClientRect()
            const center = wrapperRect.left + wrapperRect.width / 2
            let best = targets[0]
            let bestdist = Infinity
            targets.forEach(t => {
                const r = t.getBoundingClientRect()
                const cx = r.left + r.width / 2
                const d = Math.abs(cx - center)
                if (d < bestdist) {
                    bestdist = d
                    best = t
                }
            })
            const bestRect = best.getBoundingClientRect()
            const currentScroll = thumbsWrapper.scrollLeft
            const thumbCenter = bestRect.left - wrapperRect.left + bestRect.width / 2
            const desired = currentScroll + (thumbCenter - wrapperRect.width / 2)
            thumbsWrapper.scrollTo({ left: desired, behavior: "smooth" })
            allThumbs.forEach(t => t.classList.toggle("active", t.dataset.index == idx))
        }

        function updateActive() {
            allSlides.forEach(s => {
                s.style.transform = "scale(0.5)"
                s.style.opacity = "0.5"
            })
            const s = allSlides[currentSlide]
            s.style.transform = "scale(1)"
            s.style.opacity = "1"
            centerActiveThumb()
        }

        function goToSlide(target) {
            if (isAnimating) return
            isAnimating = true

            allSlides.forEach(s => {
                s.style.transition = "transform .4s, opacity .4s"
                s.style.transform = "scale(.5)"
                s.style.opacity = ".5"
            })

            setTimeout(() => {
                const from = (100 / totalSlides) * currentSlide
                const to = (100 / totalSlides) * target
                const distance = to - from
                const duration = 400
                const startTime = performance.now()

                function animate(time) {
                    const p = Math.min((time - startTime) / duration, 1)
                    const x = from + distance * p
                    gallery.style.transform = `translateX(-${x}%)`
                    if (p < 1) requestAnimationFrame(animate)
                    else {
                        currentSlide = target
                        if (currentSlide === 0) currentSlide = originalCount
                        else if (currentSlide === totalSlides - 1) currentSlide = 1
                        gallery.style.transition = "none"
                        gallery.style.transform = `translateX(-${(100 / totalSlides) * currentSlide}%)`
                        requestAnimationFrame(() => gallery.style.transition = "")
                        updateActive()
                        const ri = currentSlide - 1
                        const activeSlide = allSlides[ri]
                        if (activeSlide) {
                            activeSlide.style.transform = "scale(1)"
                            activeSlide.style.opacity = "1"
                        }
                        isAnimating = false
                    }
                }

                requestAnimationFrame(animate)
            }, 400)
        }

        thumbsWrapper.addEventListener("click", e => {
            const t = e.target.closest(".post-gallery-thumb")
            if (!t) return
            goToSlide(parseInt(t.dataset.index) + 1)
            resetAuto()
        })

        nextBtn.addEventListener("click", () => {
            goToSlide(currentSlide + 1)
            resetAuto()
        })

        prevBtn.addEventListener("click", () => {
            goToSlide(currentSlide - 1)
            resetAuto()
        })

        let auto = setInterval(() => goToSlide(currentSlide + 1), 8000)
        function resetAuto() {
            clearInterval(auto)
            auto = setInterval(() => goToSlide(currentSlide + 1), 8000)
        }

        wrapper.addEventListener("mouseenter", () => clearInterval(auto))
        wrapper.addEventListener("mouseleave", resetAuto)

        updateActive()

        function setupLightbox() {
            const lightbox = document.createElement("div")
            lightbox.className = "pg-lightbox"
            lightbox.style.cssText = `
        position:fixed; inset:0; background-color:rgba(0,0,0,.85); backdrop-filter: blur(20px);
        display:none; align-items:center; justify-content:center;
        z-index:999999; cursor:zoom-out;
      `

            const img = document.createElement("img")
            img.className = "pg-lightbox-img"
            img.style.cssText = "max-width:95%; max-height:95%; object-fit:contain; cursor:default;"

            const closeBtn = document.createElement("button")
            closeBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>`
            closeBtn.style.cssText = "position:fixed; top:20px; right:20px; width:28px; height:28px; background:transparent; color:white; border:none; cursor:pointer;"

            const prevL = document.createElement("div")
            prevL.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"></path></svg>`
            prevL.style.cssText = "position:fixed; left:30px; font-size:60px; color:white; cursor:pointer; user-select:none;"

            const nextL = document.createElement("div")
            nextL.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-arrow-right-circle" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"></path></svg>`
            nextL.style.cssText = "position:fixed; right:30px; font-size:60px; color:white; cursor:pointer; user-select:none;"

            lightbox.append(img, closeBtn, prevL, nextL)
            document.body.appendChild(lightbox)

            let lbIndex = 1

            function openLightbox(idx) {
                lbIndex = idx + 1
                img.src = slides[idx].querySelector("img").src
                lightbox.style.display = "flex"
            }

            function closeLightbox() {
                lightbox.style.display = "none"
            }

            function lbGo(delta) {
                lbIndex += delta
                if (lbIndex <= 0) lbIndex = originalCount
                else if (lbIndex >= totalSlides - 1) lbIndex = 1
                goToSlide(lbIndex)
                img.src = allSlides[lbIndex].querySelector("img").src
            }

            slides.forEach((slide, i) => {
                slide.querySelector("img").addEventListener("click", e => {
                    e.stopPropagation()
                    openLightbox(i)
                })
            })

            closeBtn.addEventListener("click", closeLightbox)
            lightbox.addEventListener("click", e => { if (e.target === lightbox) closeLightbox() })
            prevL.addEventListener("click", e => { e.stopPropagation(); lbGo(-1) })
            nextL.addEventListener("click", e => { e.stopPropagation(); lbGo(1) })

            document.addEventListener("keydown", e => {
                if (lightbox.style.display !== "flex") return
                if (e.key === "Escape") closeLightbox()
                if (e.key === "ArrowRight") lbGo(1)
                if (e.key === "ArrowLeft") lbGo(-1)
            })

            let lx = 0
            lightbox.addEventListener("touchstart", e => lx = e.touches[0].clientX, { passive: true })
            lightbox.addEventListener("touchend", e => {
                const dx = e.changedTouches[0].clientX - lx
                if (Math.abs(dx) > 50) lbGo(dx < 0 ? 1 : -1)
            })
        }
        setupLightbox()
    }

    document.querySelectorAll(".post-gallery-wrapper").forEach(postGallery)
}

document.addEventListener("DOMContentLoaded", postGalleries)