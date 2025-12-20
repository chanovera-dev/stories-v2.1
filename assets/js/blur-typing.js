document.addEventListener("DOMContentLoaded", function () {
    let firstRun = true

    function animateQuote(quoteItem) {
        const titleEl = quoteItem.querySelector(".quote-content .wp-block-heading")
        if (!titleEl) return

        const text = titleEl.textContent.trim()
        if (!text) return

        titleEl.textContent = ""
        titleEl.setAttribute("aria-label", text)

        const words = text.split(/\s+/)

        words.forEach((word, index) => {
            const wordSpan = document.createElement("span")
            wordSpan.classList.add("word")
            wordSpan.textContent = word
            wordSpan.setAttribute("aria-hidden", "false")

            wordSpan.style.display = "inline-block"
            wordSpan.style.whiteSpace = "nowrap"
            wordSpan.style.opacity = "0"
            wordSpan.style.filter = "blur(20px)"
            wordSpan.style.transition = "filter 0.6s ease-out, opacity 0.6s ease-out"

            const baseDelay = index * 0.25
            const extraDelay = firstRun ? 2.5 : 0
            wordSpan.style.transitionDelay = `${baseDelay + extraDelay}s`

            titleEl.appendChild(wordSpan)

            if (index < words.length - 1) {
                const spaceSpan = document.createElement("span")
                spaceSpan.setAttribute("aria-hidden", "true")
                spaceSpan.textContent = "\u00A0"
                spaceSpan.style.display = "inline-block"
                spaceSpan.style.opacity = "0"
                spaceSpan.style.filter = "blur(20px)"
                spaceSpan.style.transition = "filter 0.6s ease-out, opacity 0.6s ease-out"
                spaceSpan.style.transitionDelay = `${baseDelay + extraDelay}s`
                titleEl.appendChild(spaceSpan)
            }
        })

        requestAnimationFrame(() => {
            const spans = titleEl.querySelectorAll(".word, span[aria-hidden='true']")
            spans.forEach((span) => {
                span.style.opacity = "1"
                span.style.filter = "blur(0)"
            })
        })

        if (firstRun) firstRun = false
    }

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (
                mutation.type === "attributes" &&
                mutation.attributeName === "class" &&
                mutation.target.classList.contains("quote-item")
            ) {
                const el = mutation.target

                if (el.classList.contains("active")) {
                    setTimeout(() => animateQuote(el), 100)
                }
            }
        })
    })

    document.querySelectorAll(".quote-item").forEach((item) => {
        observer.observe(item, { attributes: true })
    })

    const activeOnLoad = document.querySelector(".quote-item.active")
    if (activeOnLoad) animateQuote(activeOnLoad)
})