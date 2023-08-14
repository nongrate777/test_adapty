const header__burger = document.querySelector('.header__burger');
const header__menu = document.querySelector('.header__menu');

if (header__burger) {
    header__burger.addEventListener('click', () => {
        if (header__burger.classList.contains('header__burger_active')) {
            header__burger.classList.remove('header__burger_active');
            header__menu.classList.remove('header__menu_active');
        } else {
            header__burger.classList.add('header__burger_active');
            header__menu.classList.add('header__menu_active');
        }
    });
}
