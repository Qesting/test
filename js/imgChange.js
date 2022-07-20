const min = (document.querySelectorAll("select > option").length > 1) ? 1 : 0;
const max = document.querySelectorAll("select > option").length - 1;
var active = 0;

function getMode() {
    return document.querySelector('#mode').value;
}

function setMode(newMode) {
    if (!['add', 'del'].includes(newMode)) return false;

    document.querySelector('#mode').value = newMode;
}

function error(content = "", type = "") {
	const handler = document.getElementById('error');
	
	handler.textContent = content;

	const secondClass = (type == "s") ? "alert-success" : "alert-danger";
	const remove = (type != "s") ? "alert-success" : "alert-danger";
	
	if (content.length > 0) {
		handler.classList.add('alert', secondClass);
		handler.classList.remove(remove);
	} else {
		handler.classList.remove('alert', 'alert-danger', 'alert-success');
	}
}

function sliceFname(fname) {

    const name = fname.substring(0, (fname.lastIndexOf('.') + 1) ? fname.lastIndexOf('.') : undefined);
    const ext = fname.substring((fname.lastIndexOf('.') - 1 >>> 0) + 2);

    return [name, ext];
}

function info(num) {
    
    const type = document.querySelector("#type");
    const hasimg = document.querySelector("#hasimg");

    if (document.querySelector("#imgdel") !== null) document.querySelector("#imgdel").remove();
    if (document.querySelectorAll("#options > *").length > 0) document.querySelector("#options").innerHTML = "";
    
    if (num == 0) {
        type.textContent = "";
        hasimg.textContent = "";
        document.querySelector('button[name=imgAdd]').classList.add('d-none');
        if (document.querySelector("#getimg") !== null) document.querySelector("#getimg").parentElement.remove();
        return 0;
    }

    const option = document.querySelectorAll('select > option')[num];
    type.textContent = ['jednokrotny wybór', 'wielokrotny wybór', 'tekstowe'][option.dataset.type - 1];

    if (option.dataset.imgpath.length > 0) {

        hasimg.textContent = "tak";

        const imgpath = document.createElement('p');
        imgpath.classList.add('mt-3');
        imgpath.innerHTML = `<b>Zobacz obraz:</b> <a id='getimg' href='../../usermedia/${option.dataset.imgpath}'><span class='bi-card-image'></span> ${option.dataset.imgpath}</a>`;
        hasimg.parentElement.append(imgpath);

        const imgDel = document.createElement('button');
        imgDel.classList.add('btn', 'btn-danger');
        imgDel.type = 'button';
        imgDel.name = "imgDel";
        imgDel.id = "imgdel";
        imgDel.innerHTML = "<span class='bi-file-earmark-minus'><span> Usuń obraz";
        imgDel.addEventListener('click', () => {
            setMode('del');
            document.querySelector('form').submit();
        });

        document.querySelector('.alert-info').append(imgDel);

        document.querySelector('button[name=imgAdd]').classList.add('d-none');

    } else {
        hasimg.textContent = "nie";
        if (document.querySelector("#getimg") !== null) document.querySelector("#getimg").parentElement.remove();

        let options = `<div class='form-group'>
            <div class='form-group'>
                <label class='form-label'>Nazwa pliku</label>
                <div class='input-group'>
                    <input type='text' name='fname' class='form-control' />
                    <span class='input-group-text' id='ext'></span>
                </div>
            </div>
            <div class='form-group'>
                <label class-'form-label'>Obraz w formacie PNG, JPEG lub WEBP</label>
                <input type='file' name='file' accept='image/png, image/jpeg, image/webp' class='form-control' />
            </div>
        </div>`;

        document.querySelector("#options").innerHTML = options;

        document.querySelector('input[type=file]').addEventListener('change', function() {

            if (!(document.querySelector('input[type=file]').files[0].type.search(/^image\/(png|jpeg|webp)/) + 1)) {
                error("Niedozwolony format pliku!", "e");
                document.querySelector('input[type=file]').value = "";
                return 0;
            }

            if (document.querySelector('input[type=file]').files[0].size > Math.pow(1024, 2)) {
                error("Plik nie może być większy niż 1 MiB!", "e");
                document.querySelector('input[type=file]').value = "";
                return false;
            }

            const fname = sliceFname(document.querySelector('input[type=file]').files[0].name);
            
            document.querySelector("input[name=fname]").value = fname[0];
            document.querySelector('#ext').textContent = fname[1];
        });

        document.querySelector('button[name=imgAdd]').classList.remove('d-none');
    }

}

document.querySelector("select").addEventListener('change', function() {

    active = this.value;
    info(this.value);
    
});

if (min > 0) {

    document.querySelector('#prev').addEventListener('click', () => {
        active--;

        if (active > 0) {
            document.querySelector('select').value = active;
        } else {
            document.querySelector('select').value = max;
            active = max;
        }

        info(active);
        error();
    });

    document.querySelector('#next').addEventListener('click', () => {
        active++;

        if (active <= max) {
            document.querySelector('select').value = active;
        } else {
            document.querySelector('select').value = 1;
            active = 1;
        }

        info(active);
        error();
    });
}

document.querySelector("#btnsubmit").addEventListener('click', () => {

    error();

    setMode('add');

    if (!(document.querySelector("select").value >= min && document.querySelector("select").value <= max)) {
        error("Musisz wybrać pytanie!", "e");
        return false;
    }

    const files = document.querySelector('input[type=file]').files;
    const fName = document.querySelector("input[name=fname]");

    if (files.length == 0) {
        error("Musisz wybrać plik!", 'e');
        return false;
    }

    if (files[0].size > Math.pow(1024, 2)) {
        error("Plik nie może być większy niż 1 MiB!", "e");
        return false;
    }

    if (fName.value.length == 0) {
        fName.value = sliceFname(files[0].name)[0];
    }

    if (!(files[0].type.search(/^image\/(png|jpeg|webp)/) + 1)) {
        error("Niedozwolony format pliku!", "e");
        return 0;
    }

    const namePattern = new RegExp(`[#%&\{\}\\<>*?\/\$\!'":;@+\`|=]`);
    if (namePattern.test(fName.value)) {
        error("Nazwa zawiera niedozwolone znaki!", "e");
        return false;
    }

    document.querySelector("form").submit();

});