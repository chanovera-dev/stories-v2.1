const ua = navigator.userAgent.toLowerCase()
const isChromium =
    !!window.chrome && /chrome|crios|crmo|edg|brave|opera|opr|vivaldi/i.test(ua)

if (isChromium) {
    document.addEventListener('DOMContentLoaded', () => {
        document.body.classList.add('is-chromium')
    })
}