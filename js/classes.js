buildLayout("classes.html");

function classNameTaken(name, skipId) {
    var classes = loadData("classes", []);

    for (var i = 0; i < classes.length; i++) {
        if (classes[i].id != skipId && classes[i].name.toLowerCase() == name.toLowerCase()) {
            return true;
        }
    }
    return false;
}


async function addClass() {
    var name = document.getElementById("className").value.trim();
    var time = document.getElementById("classTime").value.trim();
    var color = document.getElementById("classColorInput").value;

    if (name == "" || time == "") {
        alert("Please fill in both boxes");
        return;
    }

    if (classNameTaken(name, 0)) {
        alert("You already have a class with that name");
        return;
    }

    var formData = new FormData();
    formData.append("name", name);
    formData.append("time", time);
    formData.append("color", color);

    try {
        var response = await fetch("../php/add_class.php", {
            method: "POST",
            body: formData
        });

        if (!response.ok) {
            throw new Error("Server error");
        }

        var result = await response.json();

        if (!result.success) {
            alert(result.error || "Could not add class");
            return;
        }

        alert("Class added");

        document.getElementById("className").value = "";
        document.getElementById("classTime").value = "";

        await showPage();

    } catch (error) {
        console.error(error);
        alert("Could not connect to the server");
    }
}


function changeClassColor(id, color) {
    var classes = loadData("classes", []);
    var item = findById(classes, id);
    item.color = color;
    saveData("classes", classes);
    showPage();
}

function renameInList(key, oldName, newName) {
    var list = loadData(key, []);

    for (var i = 0; i < list.length; i++) {
        if (list[i].className == oldName) {
            list[i].className = newName;
        }
    }
    saveData(key, list);
}

function editClass(id) {
    var classes = loadData("classes", []);
    var item = findById(classes, id);

    var newName = prompt("Change the class name:", item.name);
    if (newName == null) {
        return;
    }
    newName = newName.trim();
    if (newName == "") {
        alert("The class name can't be empty");
        return;
    }
    if (classNameTaken(newName, id)) {
        alert("You already have a class with that name");
        return;
    }

    var newTime = prompt("Change the meeting time:", item.time);
    if (newTime == null) {
        return;
    }

    var oldName = item.name;
    item.name = newName;
    item.time = newTime.trim();
    saveData("classes", classes);

    renameInList("assignments", oldName, newName);
    renameInList("grades", oldName, newName);
    showPage();
}

function removeClass(id) {
    var classes = loadData("classes", []);
    var item = findById(classes, id);

    if (!confirm("Remove " + item.name + " and its grades?")) {
        return;
    }

    var grades = loadData("grades", []);
    var keptGrades = [];
    for (var i = 0; i < grades.length; i++) {
        if (grades[i].className != item.name) {
            keptGrades.push(grades[i]);
        }
    }
    saveData("grades", keptGrades);

    renameInList("assignments", item.name, "");
    saveData("classes", removeById(classes, id));
    showPage();
}

function countOpenAssignments(className) {
    var assignments = loadData("assignments", []);
    var count = 0;

    for (var i = 0; i < assignments.length; i++) {
        if (assignments[i].className == className && assignments[i].done == false) {
            count = count + 1;
        }
    }
    return count;
}

function makeClassCard(item) {
    var card = makeElement("div", "", "class-card");
    card.appendChild(makeElement("h3", item.name));
    card.appendChild(makeElement("p", item.time));
    card.appendChild(makeElement("p", "Open assignments: " + countOpenAssignments(item.name)));

    var average = classAverage(item.name);
    if (average == null) {
        card.appendChild(makeElement("p", "Current grade: no grades yet"));
    } else {
        card.appendChild(makeElement("p", "Current grade: " + describeGrade(average)));
    }

    var picker = document.createElement("input");
    picker.type = "color";
    picker.value = item.color;
    picker.title = "Change color";
    picker.onchange = function () {
        changeClassColor(item.id, picker.value);
    };

    var actions = makeElement("div", "", "actions");
    actions.appendChild(picker);
    actions.appendChild(makeButton("Edit", function () {
        editClass(item.id);
    }));
    actions.appendChild(makeButton("Remove", function () {
        removeClass(item.id);
    }));
    card.appendChild(actions);

    return card;
}

async function showPage() {
    var list = document.getElementById("classList");
    list.innerHTML = "Loading classes...";

    try {
        var response = await fetch("../php/classes.php");

        if (!response.ok) {
            throw new Error("Server error");
        }

        var classes = await response.json();

        saveData("classes", classes);

        document.getElementById("classColorInput").value =
            classColors[classes.length % classColors.length];

        list.innerHTML = "";

        if (classes.length == 0) {
            list.appendChild(makeElement("p", "No classes yet. Add one above!", "empty-message"));
        }

        for (var i = 0; i < classes.length; i++) {
            list.appendChild(makeClassCard(classes[i]));
        }

    } catch (error) {
        console.error(error);
        list.innerHTML = "Could not load classes from the database.";
    }
}

showPage();