var panes = 0;
aTab = 1;

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

function addAnswer(pane) {

	const type = parseInt(pane.dataset.type, 10);

	if (![1, 2].includes(type)) return false;
	
	const target = pane.querySelector(".card-body > .form-group:nth-last-of-type(2)");
	
	if (target.querySelectorAll(".form-group").length == 6) return false;
	
	const ans = document.createElement('div');
	ans.classList.add("form-group");
	
	const num = pane.id.substring(1);
	
	ans.innerHTML = `<div class='input-group'>
		<span class='input-group-text'><input value='${pane.querySelectorAll(".input-group-text").length + 1}' type='${(type === 1) ? 'radio' : 'checkbox'}' name='ql[${num}][answer]${(type === 2) ? "[]" : ""}' /></span>
		<input type='text' name='ql[${num}][answers][]' class='form-control' />
		${(target.childElementCount > 2) ? "<button type='button' class='btn btn-danger' onclick='deleteAnswer(getElementById(\"" + pane.id +"\"), " + (target.childElementCount + 1) +")'><span class='bi-dash-square'></span></button>" : ""}
	</div>`;

	target.append(ans);
	
}

function deleteAnswer(pane, ansNum) {
	if (![1, 2].includes(parseInt(pane.dataset.type))) return false;
	
	const target = pane.querySelector(".card-body > .form-group:nth-last-of-type(2)");
	
	if (target.querySelectorAll(".form-group").length == 2) return false;
	
	const ans = target.querySelector(".form-group:nth-of-type(" + ansNum + ")");
	ans.remove();
}

function switchPane(nT) {
	document.querySelector(`#q${aTab}`).classList.add("d-none");
	document.querySelector(`#q${nT}`).classList.remove("d-none");
	aTab = nT;
	
	document.querySelector('#add').parentElement.classList.remove("d-none");
	
	document.getElementById("qContent").value = "";
	document.getElementById("qType").value = 0;
}

function addPane(type, content = "") {

	if (![1, 2, 3].includes(type)) return false;
	
	const num = document.querySelectorAll("#cards > .card").length + 1;
	
	const pane = document.createElement("div");
	pane.classList.add("card");
	
	pane.id = "q" + num;
	pane.dataset.type = type;
	
	let paneContent = `<div class='card-header d-flex flex-column'>
		<h4 class='mt-4'>Pytanie ${num} (${['j. wybór', 'w. wybór', 'tekstowe'][type - 1]})</h4>
		<button type='button' class='btn btn-danger btn-del align-self-end'>Usuń pytanie</button>
	</div>
	<div class='card-body pt-4 px-4'>
	<input type='hidden' name='ql[${num}][type] value='${type}' />
		<div class='form-group'>
			<label class='form-label'>Treść pytania</label>
			<input type='text' class='form-control' value='${content}' name='ql[${num}][content]' />
		</div>
		<div class='form-group'>
			<label class='form-label'>Wartość odpowiedzi</label>
			<input type='number' min='1' max='5' value='1' class='form-control' name='ql[${num}][points]' />
		</div>
		<div class="form-group">
			<label class='form-label'>Odpowiedzi</label>
			${(type === 3) ? "<div class='form-group'><input type='text' name='ql[" + num +"][answer]' class='form-control' /></div>" : ""}
		</div>
		<div class='form-group'><div class='btn-group'><button class='btn btn-secondary btn-block' onclick='addAnswer(document.getElementById("${pane.id}"))' type='button'>Dodaj odpowiedź</button></div></div>
	</div>`;
	
	pane.innerHTML = paneContent;
	
	pane.querySelector(".btn-del").addEventListener('click', () => {
		deletePane(document.getElementById(pane.id));
	});
	
	if (type !== 3) {
		addAnswer(pane);
		addAnswer(pane);
	}
	
	document.querySelector("#cards").append(pane);
	panes++;
	document.querySelector("#count").textContent = ` (${panes} pytań)`;
	
	return pane;
}

function deletePane(pane) {
	
	if (!pane.classList.contains('card')) return false;
	
	const paneId = parseInt(pane.id.substring(1), 10);
	const newPaneId = (paneId == panes) ? paneId - 1 : paneId + 1;
	
	switchPane(newPaneId);
	if (newPaneId == 0) document.querySelector("#add").parentElement.classList.add('d-none');

	pane.remove();
	
	const headers = document.querySelectorAll("#cards .card-header > h4");
	for (i = 0; i < headers.length; i++) {
		headers[i].textContent = `Pytanie ${i + 1} (${['j. wybór', 'w. wybór', 'tekstowe'][headers[i].parentElement.parentElement.dataset.type - 1]})`;
	}
	
	document.querySelector(`#nav-tabs > .nav-item:nth-child(${paneId})`).remove();
	
	const cards = document.querySelectorAll("#cards > .card");
	const tabs = document.querySelectorAll('#nav-tabs > .nav-item');
	
	for (i = 0; i < cards.length; i++) {
		cards[i].id = `q${i + 1}`;
		tabs[i].dataset.href = cards[i].id;
		tabs[i].textContent = i + 1
	}
	
	panes--;
	document.querySelector("#count").textContent = ` (${panes} pytań)`;
}

function addTab(pane) {
	const nav = document.querySelector("#nav-tabs");
	
	const btn = document.createElement("button");
	btn.classList.add("nav-item", "nav-link");
	btn.textContent = pane.id.substring(1);
	btn.dataset.href = pane.id;
	btn.type = "button";
 	btn.addEventListener('click', function() {
		error();
		const id = parseInt(this.dataset.href.substring(1), 10);
		switchPane(id);
	});
	
	nav.append(btn);
}

document.addEventListener("DOMContentLoaded", () => {
	const list = document.querySelectorAll("#cards > .card");
	panes = list.length;

	document.querySelector("#count").textContent = ` (${panes} pytań)`;
	
	if (panes == 0) {
		document.querySelector("#add").parentElement.classList.add('d-none');
		document.querySelector("#q0").classList.remove('d-none');
		aTab = 0;
		return false;
	} 

	for (let el of list) {
		addTab(el);
		if (el.id != "q1") el.classList.add('d-none');
	};
});

document.querySelector("#prev").addEventListener('click', () => {
	let newTab = (aTab - 1 == 0) ? panes : aTab - 1;
	switchPane(newTab);
	error();
});

document.querySelector("#next").addEventListener('click', () => {
	let newTab = (aTab + 1 > panes) ? 1 : aTab + 1;
	switchPane(newTab);
	error();
});

document.querySelector('#add').addEventListener('click', function() {
	switchPane(0);
		this.parentElement.classList.add('d-none');
});

document.querySelector('#addQuestion').addEventListener('click', () => {
	const content = document.getElementById("qContent").value;
	const type = parseInt(document.getElementById("qType").value, 10);
	
	if (type == 0) {
		error("Musisz wybrać typ pytania!", "e");
		return false;
	}
	
	addTab(addPane(type, content));
	
	error("Pomyślnie dodano pytanie!", "s");
	
	switchPane(panes);
	
});

document.querySelector("#testdel").addEventListener('click', () => {
	if (confirm("Czy jesteś tego pewien?\nTa operacja jest nieodwracalna.")) location.href = "testdel.php";
});

document.querySelector("#testmod").addEventListener('click', () => {
	location.href = "testmod.php";
});

document.querySelector("#testimg").addEventListener('click', () => {
	location.href = "imgadd.php";
});