const wrapper = document.querySelector('.clouds--wrapper')
const clouds = document.querySelector('.clouds')

const cloudWidth = clouds.offsetWidth
let speed = 0.5

const clouds2 = clouds.cloneNode(true)
const clouds3 = clouds.cloneNode(true)
wrapper.appendChild(clouds2)
wrapper.appendChild(clouds3)

let cloudsArray = [clouds, clouds2, clouds3]
cloudsArray.forEach((c, i) => {
    c.style.position = "absolute"
    c.style.left = `${i * cloudWidth}px`
})

function animate() {
    cloudsArray.forEach(c => {
        let current = parseFloat(c.style.left) || 0
        current += speed
        c.style.left = `${current}px`

        if (current >= cloudWidth * 2) {
            c.style.left = `${-cloudWidth}px`
        }
    })

    requestAnimationFrame(animate)
}

animate()