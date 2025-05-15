let results = document.getElementById("results") as HTMLElement;

/***************************************************/
// Define Advanced Types and Search function here
/***************************************************/

/******************************************/
//            Part B
/******************************************/

// Note: the ? allows (in Typescript) for the fact that
// getElementById could return null
// Selecting the result div
const resultDiv = document.getElementById("results");

// Event Listener for Part B Button
document.getElementById("partb")?.addEventListener("click", () => {
    let name: string = "Dikshith Reddy M"; // name
    let id: number = 789055; // student ID

    // Displaying the result in the HTML
    if (resultDiv) {
        resultDiv.innerHTML = `${name} has student number ${id}.`;
    }
});


/******************************************/
//            Part C
/******************************************/

// Define Advanced Types
type Grade = [string, number]; // Tuple
enum Year {
    First = 1,
    Second,
    Third,
    Fourth
}
type FeeType = "Domestic" | "International";

// Event Listener for Part C Button
document.getElementById("partc")?.addEventListener("click", () => {
    let name: string = "Dikshith Reddy M";
    let id: number = 789055;

    let currentYear: Year = Year.Third; //  Third Year
    let tuition: FeeType = "International"; // 

    let grades: Grade[] = [
        ["COIS1010", 85],
        ["COIS3430", 92]
    ];

    // Output
    if (resultDiv) {
        resultDiv.innerHTML = `${name} has student number ${id}. They are in year ${currentYear} of their studies, pay ${tuition} tuition, and got ${grades[1][1]} in ${grades[1][0]}.`;
    }
});


/******************************************/
//            Part D
/******************************************/

// Define Student Interface
interface Student {
    name: string;
    readonly id: number;
    currentYear: Year;
    tuition: FeeType;
    grades: Grade[];
    scholarship?: string; // Optional property
}

// Event Listener for Part D Button
document.getElementById("partd")?.addEventListener("click", () => {
    let student1: Student = {
        name: "Dikshith Reddy M",
        id: 789055,
        currentYear: Year.Third,
        tuition: "International",
        grades: [["COIS1010", 85], ["COIS3430", 92]],
        scholarship: "President's Scholarship"
    };

    let student2: Student = {
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
function search(term: string | number, students: Student[]): Student | undefined {
    for (let student of students) {
        if ((typeof term === "number" && student.id === term) || 
            (typeof term === "string" && student.name === term)) {
            return student;
        }
    }
    return undefined;
}


// Event Listener for Part E Button
document.getElementById("parte")?.addEventListener("click", () => {
    let students: Student[] = [
        { name: "Dikshith Reddy M", id: 789055, currentYear: Year.Third, tuition: "International", grades: [["COIS1010", 85], ["COIS3430", 92]] },
        { name: "Alex", id: 654321, currentYear: Year.Second, tuition: "Domestic", grades: [["COIS1020", 78], ["COIS3440", 88]] }
    ];

    let searchResult = search(789055, students); // Searching by ID

    if (resultDiv) {
        if (searchResult) {
            resultDiv.innerHTML = `${searchResult.name} has student number ${searchResult.id}. They are in year ${searchResult.currentYear} of their studies, pay ${searchResult.tuition} tuition, and got ${searchResult.grades[1][1]} in ${searchResult.grades[1][0]}.`;
        } else {
            resultDiv.innerHTML = "This student couldn't be found.";
        }
    }
});
