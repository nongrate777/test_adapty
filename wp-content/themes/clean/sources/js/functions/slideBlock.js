export function slideToggle(e, time = 300) {
    if (getComputedStyle(e).display == 'none') {
        slideShow(e, time);
    } else {
        slideHide(e, time);
    }
}

export function slideShow(e, time = 300) {
    e.style.overflow = 'hidden';

    if (getComputedStyle(e).display == 'none') {
        e.style.display = 'block';

        const h = +getComputedStyle(e).height.replace('px', "");

        setCss(e, '0px', '0px', '0px', '0px', '0px', '0px', '0px');

        setTimeout(() => {
            e.style.transition = time + 'ms';
            setCss(e, h + 'px');
        }, 0);

        setTimeout(() => {
            e.style.display = 'block';
            setCss(e);
            e.style.overflow = '';
            e.style.transition = '';
        }, time);
    }
}

export function slideHide(e, time = 300) {
    e.style.overflow = 'hidden';

    if (getComputedStyle(e).display != 'none') {
        const h = +getComputedStyle(e).height.replace('px', "");

        e.style.height = h + 'px';

        setTimeout(() => {
            e.style.transition = time + 'ms';
            setCss(e, '0px', '0px', '0px', '0px', '0px', '0px', '0px');
        }, 0);


        setTimeout(() => {
            e.style.display = 'none';
            setCss(e);
            e.style.overflow = '';
            e.style.transition = '';
        }, time);
    }
}

function setCss(e, h = '', pT = '', pB = '', mT = '', mB = '', bT = '', bB = '') {
    e.style.height = h;
    e.style.paddingTop = pT;
    e.style.paddingBottom = pB;
    e.style.marginTop = mT;
    e.style.marginBottom = mB;
    e.style.borderTopWidth = bT;
    e.style.borderBottomWidth = bB;
}
