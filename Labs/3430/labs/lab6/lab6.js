var _a, _b, _c, _d;
var results = document.getElementById("results");
/***************************************************/
// Define Advanced Types and Search function here
/***************************************************/
/******************************************/
//            Part B
/******************************************/
// Note: the ? allows (in Typescript) for the fact that
// getElementById could return null
// Selecting the result div
var resultDiv = document.getElementById("results");
// Event Listener for Part B Button
(_a = document.getElementById("partb")) === null || _a === void 0 ? void 0 : _a.addEventListener("click", function () {
    var name = "Dikshith Reddy M"; // name
    var id = 789055; // student ID
    // Displaying the result in the HTML
    if (resultDiv) {
        resultDiv.innerHTML = "".concat(name, " has student number ").concat(id, ".");
    }
});
var Year;
(function (Year) {
    Year[Year["First"] = 1] = "First";
    Year[Year["Second"] = 2] = "Second";
    Year[Year["Third"] = 3] = "Third";
    Year[Year["Fourth"] = 4] = "Fourth";
})(Year || (Year = {}));
// Event Listener for Part C Button
(_b = document.getElementById("partc")) === null || _b === void 0 ? void 0 : _b.addEventListener("click", function () {
    var name = "Dikshith Reddy M";
    var id = 789055;
    var currentYear = Year.Third; //  Third Year
    var tuition = "International"; // 
    var grades = [
        ["COIS1010", 85],
        ["COIS3430", 92]
    ];
    // Output
    if (resultDiv) {
        resultDiv.innerHTML = "".concat(name, " has student number ").concat(id, ". They are in year ").concat(currentYear, " of their studies, pay ").concat(tuition, " tuition, and got ").concat(grades[1][1], " in ").concat(grades[1][0], ".");
    }
});
// Event Listener for Part D Button
(_c = document.getElementById("partd")) === null || _c === void 0 ? void 0 : _c.addEventListener("click", function () {
    var student1 = {
        name: "Dikshith Reddy M",
        id: 789055,
        currentYear: Year.Third,
        tuition: "International",
        grades: [["COIS1010", 85], ["COIS3430", 92]],
        scholarship: "President's Scholarship"
    };
    var student2 = {
        name: "Alex",
        id: 654321,
        currentYear: Year.Second,
        tuition: "Domestic",
        grades: [["COIS1020", 78], ["COIS3440", 88]]
    };
    // Logging students to console
    console.log("Student 1:", student1);
    console.log("Student 2:", student2);
    if (resultDiv) {
        resultDiv.innerHTML = "Check the console for the student objects.";
    }
});
/******************************************/
//            Part E
/******************************************/
// Search Function
function search(term, students) {
    for (var _i = 0, students_1 = students; _i < students_1.length; _i++) {
        var student = students_1[_i];
        if ((typeof term === "number" && student.id === term) ||
            (typeof term === "string" && student.name === term)) {
            return student;
        }
    }
    return undefined;
}
// Event Listener for Part E Button
(_d = document.getElementById("parte")) === null || _d === void 0 ? void 0 : _d.addEventListener("click", function () {
    var students = [
        { name: "Dikshith Reddy M", id: 789055, currentYear: Year.Third, tuition: "International", grades: [["COIS1010", 85], ["COIS3430", 92]] },
        { name: "Alex", id: 654321, currentYear: Year.Second, tuition: "Domestic", grades: [["COIS1020", 78], ["COIS3440", 88]] }
    ];
    var searchResult = search(789055, students); // Searching by ID
    if (resultDiv) {
        if (searchResult) {
            resultDiv.innerHTML = "".concat(searchResult.name, " has student number ").concat(searchResult.id, ". They are in year ").concat(searchResult.currentYear, " of their studies, pay ").concat(searchResult.tuition, " tuition, and got ").concat(searchResult.grades[1][1], " in ").concat(searchResult.grades[1][0], ".");
        }
        else {
            resultDiv.innerHTML = "This student couldn't be found.";
        }
    }
});
