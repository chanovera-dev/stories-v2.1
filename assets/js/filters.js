function propertiesSubmenusToggle() {
    document.querySelectorAll(".properties--filter .property-filter-form .filter-navigation.menu  > .menu-item-has-children > .sub-menu .menu-item-has-children .wrapper-for-title .button-for-submenu").forEach(button => {
        button.addEventListener("click", () => {
            const menuItem = button.closest("li");
            const subMenu = menuItem?.querySelector(".sub-menu");
            if (!subMenu) return;

            const isOpen = subMenu.classList.contains("open");
            const itemCount = subMenu.childElementCount;
            const duration = (isOpen ? 0.1 : 0.1) * itemCount;

            subMenu.style.transition = `max-height ${duration}s cubic-bezier(0.4, 0, 0.2, 1), padding-top .3s ease`;
            subMenu.classList.toggle("open");
            button.classList.toggle("active");
            button.classList.toggle("rotate");
        });
    });
}
document.addEventListener("DOMContentLoaded", propertiesSubmenusToggle);

function propertiesPrimaryMenusToggle() {
    document.querySelectorAll(".properties--filter .property-filter-form .filter-navigation.menu  > .menu-item-has-children > .button-for-submenu").forEach(button => {
        button.addEventListener("click", () => {
            const menuItem = button.closest("li");
            const subMenu = menuItem?.querySelector(".sub-menu");
            if (!subMenu) return;

            const isOpen = subMenu.classList.contains("open");
            const itemCount = subMenu.childElementCount;
            const duration = (isOpen ? 0.1 : 0.1) * itemCount;

            subMenu.style.transition = `max-height ${duration}s cubic-bezier(0.4, 0, 0.2, 1), padding-top .3s ease, border-color .3s ease`;
            subMenu.classList.toggle("close");
            button.classList.toggle("rotate");
        });
    });
}
document.addEventListener("DOMContentLoaded", propertiesPrimaryMenusToggle);