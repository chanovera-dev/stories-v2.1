const body = document.body

const ua = navigator.userAgent.toLowerCase()
const isChromium =
    (!!window.chrome || /crios/i.test(ua)) &&
    /chrome|crios|crmo|edg|brave|opera|opr|vivaldi/i.test(ua)

if (isChromium) {
    document.addEventListener('DOMContentLoaded', () => {
        body.classList.add('is-chromium')
    })
}

function scrollActions() {
    let last = 0
    let ticking = false

    function onScroll() {
        const y = window.scrollY
        if (y <= 0) {
            body.classList.remove('scroll-up', 'scroll-down')
        } else if (y > last) {
            body.classList.add('scroll-down')
            body.classList.remove('scroll-up')
        } else {
            body.classList.add('scroll-up')
            body.classList.remove('scroll-down')
        }
        last = y
        ticking = false
    }

    function handleScroll() {
        if (!ticking) {
            requestAnimationFrame(onScroll)
            ticking = true
        }
    }

    window.addEventListener('scroll', handleScroll, { passive: true })

    return () => window.removeEventListener('scroll', handleScroll)
}
scrollActions()

function toggleMenuMobile() {
    const button = document.querySelector('.menu-mobile__button')
    const menu = document.querySelector('.main-navigation')

    if (!button || !menu) return

    const isActive = button.classList.toggle('active')
    menu.classList.toggle('open')

    if (isActive) {
        setTimeout(() => {
            document.addEventListener('click', handleClickOutsideMenu)
        }, 10)
    } else {
        document.removeEventListener('click', handleClickOutsideMenu)
    }
}

function handleClickOutsideMenu(e) {
    const button = document.querySelector('.menu-mobile__button')
    const menu = document.querySelector('.main-navigation')

    if (!menu || !button) return

    const clickedInsideMenu = menu.contains(e.target)
    const clickedToggleButton = button.contains(e.target)

    if (!clickedInsideMenu && !clickedToggleButton) {
        closeMenuMobile()
    }
}

function handleClickOutsideSearch(e) {
    const button = document.querySelector('.search-mobile__button')
    const searchform = document.querySelector('#custom-searchform')

    if (!searchform || !button) return

    const clickedSearchform = searchform.contains(e.target)
    const clickedToggleButton = button.contains(e.target)

    if (!clickedSearchform && !clickedToggleButton) {
        closeCustomSearchform()
    }
}

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' || event.key === 'Esc') {
        if (typeof closeCustomSearchform === 'function') {
            closeCustomSearchform()
            closeMenuMobile()
        }
    }
})

function toggleCustomSearchform() {
    const button = document.querySelector('.search-mobile__button')
    const searchform = document.querySelector('#custom-searchform')

    if (!button || !searchform) return

    const isActive = button.classList.toggle('active')
    searchform.classList.toggle('show')

    if (isActive) {
        setTimeout(() => {
            document.addEventListener('click', handleClickOutsideSearch)
        }, 10)
    } else {
        document.removeEventListener('click', handleClickOutsideSearch)
    }
}

function closeCustomSearchform() {
    const button = document.querySelector('.search-mobile__button')
    const searchform = document.querySelector('#custom-searchform')

    if (button) button.classList.remove('active')
    if (searchform) searchform.classList.remove('show')

    document.removeEventListener('click', handleClickOutsideSearch)
}

function menuWithChildren() {
    const menuItems = document.querySelectorAll('#main-header .block .content .main-navigation .menu-item-has-children')

    menuItems.forEach(item => {
        item.addEventListener('click', function (e) {

            if (e.target.tagName === 'A') {
                return;
            }

            e.preventDefault()
            e.stopPropagation()

            item.classList.toggle('open')

            const subMenu = item.querySelector('.sub-menu')
            if (subMenu) {
                const childrenCount = subMenu.children.length
                const transitionTime = childrenCount * 0.1
                subMenu.style.transition = `max-height ${transitionTime}s ease-in-out`
                subMenu.classList.toggle('open')
            }
        })
    })
}
document.addEventListener('DOMContentLoaded', menuWithChildren)

function closeMenuMobile() {
    const button = document.querySelector('.menu-mobile__button')
    const menu = document.querySelector('.main-navigation')

    if (!button || !menu) return

    button.classList.remove('active')
    menu.classList.remove('open')

    document.removeEventListener('click', handleClickOutsideMenu)
}