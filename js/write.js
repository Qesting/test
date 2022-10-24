var currentTextArea = null;
var changed = false;

// ---- ALERT
const error = mess => {
    const eHandler = document.getElementById('error');
    eHandler.textContent = mess;
    eHandler.classList.add('alert', 'alert-danger');
}

// ---- HANDLES
// -- TOOLBAR
// SHOW
const show = document.getElementById('help');
const toolbar = document.getElementById('toolbar');
// BUTTONS
const h1 = document.getElementById('h1');
const h2 = document.getElementById('h2');
const h3 = document.getElementById('h3');
const h4 = document.getElementById('h4');
const h5 = document.getElementById('h5');
const h6 = document.getElementById('h6');
const bold = document.getElementById('bold');
const italic = document.getElementById('italic');
const strike = document.getElementById('strike');
const quote = document.getElementById('quote');
const ol = document.getElementById('ol');
const ul = document.getElementById('ul');
const img = document.getElementById('img');
const lnk = document.getElementById('lnk');
const code = document.getElementById('code');
const hr = document.getElementById('hr');
// -- FORM
// INPUTS
const title = document.getElementById('title');
const summary = document.getElementById('summary');
const content = document.getElementById('content');
// COUNTERS
const countTitle = document.getElementById('title-count');
const countSummary = document.getElementById('summary-count');
const countContent = document.getElementById('content-count');

// ---- SHOW OR HIDE TOOLBAR
show.addEventListener('change', () => {
    toolbar.classList.toggle('hidden');
    document.querySelector('.container:not(#cont)').classList.toggle('down');
    currentTextArea.focus();
});

// ---- PREVENT TAB + CURRENT AREA
[summary, content].forEach(n => {
    n.addEventListener('keydown', e => {
        if (e.key === 'Tab') {
            e.preventDefault();
            const start = n.selectionStart;
            const end = n.selectionEnd;
            const val = n.value;

            n.value = val.substring(0, start) + '\t' + val.substring(end);
            n.selectionStart = n.selectionEnd = start + 1;
        }
    });
    n.addEventListener('focusin', () => currentTextArea = n);
});

// ---- COUNTERS
const stringSize = value => {
    let match = value.match(/[^\x00-\x7F]/ig);
    let matched = match !== null ? match.length : 0;
    return value.length + matched;
}

[countTitle, countContent, countSummary].forEach(n => {
    const target = document.getElementById(n.dataset.target);
    const limit = n.dataset.limit;
    n.textContent = `0/${limit}`;
    let count = stringSize(target.value);
    if (count > limit) {
        target.value = target.value.substring(0,count);
    }
    n.textContent = `${count > limit ? limit : count}/${limit}`;
    target.addEventListener('input', () => {
        if (!changed) changed = true;
        let count = stringSize(target.value);
        if (count > limit) {
            target.value = target.value.substring(0,limit);
        }
        n.textContent = `${count > limit ? limit : count}/${limit}`;
    });
});

// ---- FORMATTING
// -- HEADERS
[h1, h2, h3, h4, h5, h6].forEach(n => {
    n.addEventListener('click', () => {
        const start = currentTextArea.selectionStart;
        const end = currentTextArea.selectionEnd;
        const val = currentTextArea.value;

        const selected = val.substring(start,end);
        const v2 = val.substring(0,start)
        +(/(\n\r?)+\s*$/.test(val.substring(0,start)) ? '' : '\n\n')
        +'#'.repeat(n.dataset.count)
        +' '+selected+(start !== end ? '\n\n' : '');
        currentTextArea.value = v2+val.substring(end);
        currentTextArea.selectionStart = v2.length + 1;
        currentTextArea.selectionEnd = v2.length;
        currentTextArea.focus();
    });
});
// -- STYLE
[bold, italic, strike].forEach(n => {
    n.addEventListener('click', () => {
        const start = currentTextArea.selectionStart;
        const end = currentTextArea.selectionEnd;
        const val = currentTextArea.value;

        const selected = val.substring(start,end);
        const v2 = val.substring(0,start)+n.dataset.template.replace('$',selected);
        currentTextArea.value = v2+val.substring(end);
        currentTextArea.selectionStart = v2.length + 1;
        currentTextArea.selectionEnd = v2.length;
        currentTextArea.focus();
    });
});

quote.addEventListener('click', () => {
    const start = currentTextArea.selectionStart;
    const end = currentTextArea.selectionEnd;
    const val = currentTextArea.value;

    const selected = val.substring(start,end);
    const v2 = val.substring(0,start)
    +(/(\n\r?)+\s*$/.test(val.substring(0,start)) ? '' : '\n\n')
    +'> '+selected;
    currentTextArea.value = v2+val.substring(end);
    currentTextArea.selectionStart = v2.length + 1;
    currentTextArea.selectionEnd = v2.length;
    currentTextArea.focus();
});

// -- LIST
ol.addEventListener('click', () => {
    const start = currentTextArea.selectionStart;
    const end = currentTextArea.selectionEnd;
    const val = currentTextArea.value;

    const selected = val.substring(start,end);
    let sNew = [];
    const mNum = selected.match(/.+ {2,}\n\r?|.+$/g);
    if (mNum.length !== 0) {
        mNum.forEach((n, i) => sNew.push((i+1)+'. '+n+'  '));
        sNew = sNew.join('');
    } else sNew = "";
    const v2 = val.substring(0,start)+sNew;
    currentTextArea.value = v2+val.substring(end);
    currentTextArea.selectionStart = v2.length + 1;
    currentTextArea.selectionEnd = v2.length;
    currentTextArea.focus();
});

ul.addEventListener('click', () => {
    const start = currentTextArea.selectionStart;
    const end = currentTextArea.selectionEnd;
    const val = currentTextArea.value;

    const selected = val.substring(start,end);
    let sNew = [];
    const mNum = selected.match(/.+ {2,}\n\r?|.+$/g);
    if (mNum.length !== 0) {
        mNum.forEach((n) => sNew.push('* '+n+'  '));
        sNew = sNew.join('');
    } else sNew = "";
    const v2 = val.substring(0,start)+sNew;
    currentTextArea.value = v2+val.substring(end);
    currentTextArea.selectionStart = v2.length + 1;
    currentTextArea.selectionEnd = v2.length;
    currentTextArea.focus();
});

// -- INSERT
img.addEventListener('click', () => {
    const start = currentTextArea.selectionStart;
    const end = currentTextArea.selectionEnd;
    const val = currentTextArea.value;

    const v2 = val.substring(0,start)
    +' ![alt](adres) ';
    currentTextArea.value = v2+val.substring(end);
    currentTextArea.selectionStart = v2.length + 1;
    currentTextArea.selectionEnd = v2.length;
    currentTextArea.focus();
});

lnk.addEventListener('click', () => {
    const start = currentTextArea.selectionStart;
    const end = currentTextArea.selectionEnd;
    const val = currentTextArea.value;

    const v2 = val.substring(0,start)
    +' [tekst linku](adres) ';
    currentTextArea.value = v2+val.substring(end);
    currentTextArea.selectionStart = v2.length + 1;
    currentTextArea.selectionEnd = v2.length;
    currentTextArea.focus();
});

code.addEventListener('click', () => {
    const start = currentTextArea.selectionStart;
    const end = currentTextArea.selectionEnd;
    const val = currentTextArea.value;

    const selected = val.substring(start,end);
    const v2 = val.substring(0,start)+"\`\`$\`\`".replace('$',selected);
    currentTextArea.value = v2+val.substring(end);
    currentTextArea.selectionStart = v2.length + 1;
    currentTextArea.selectionEnd = v2.length;
    currentTextArea.focus();
});

hr.addEventListener('click', () => {
    const start = currentTextArea.selectionStart;
    const end = currentTextArea.selectionEnd;
    const val = currentTextArea.value;

    const v2 = val.substring(0,start)
    +'\n\n---\n\n';
    currentTextArea.value = v2+val.substring(end);
    currentTextArea.selectionStart = v2.length + 1;
    currentTextArea.selectionEnd = v2.length;
    currentTextArea.focus();
});

// ---- PROGRESS
window.onbeforeunload = function (e) {
    if (changed) {
        var e = e || window.event;
        if (e) {
        e.returnValue = 'Czy na pewno chcesz wyjść? Twoja praca nie zostanie zapisana.';
        }
        return 'Czy na pewno chcesz wyjść? Twoja praca nie zostanie zapisana.';
    }
  };

// -- THOUGH:
document.querySelectorAll('button[type=submit]').forEach(n => {
    n.addEventListener('click', e => {
        if (title.value.length === 0 || summary.value.length === 0 || content.value.length === 0) {
            e.preventDefault();
            error("Wszystkie pola muszą zawierać treść.");
            return;
        }
        window.onbeforeunload = null;
    });
});