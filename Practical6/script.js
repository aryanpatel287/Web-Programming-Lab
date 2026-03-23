var calculatorExpression = "";

function updateCalculatorDisplay(value) {
    var displayElement = document.getElementById("calculatorDisplay");
    displayElement.value = value || "0";
}

function appendValue(value) {
    if (calculatorExpression === "Error") {
        calculatorExpression = "";
    }

    if (value === ".") {
        var currentNumber = calculatorExpression.split(/[+\-*/%]/).pop();

        if (currentNumber.indexOf(".") !== -1) {
            return;
        }

        if (currentNumber === "") {
            calculatorExpression += "0";
        }
    }

    calculatorExpression += value;
    updateCalculatorDisplay(calculatorExpression);
}

function clearCalculator() {
    calculatorExpression = "";
    updateCalculatorDisplay("0");
    document.getElementById("simpleResult").textContent = "Use the calculator buttons to perform operations.";
}

function deleteLastCharacter() {
    if (calculatorExpression === "Error") {
        clearCalculator();
        return;
    }

    calculatorExpression = calculatorExpression.slice(0, -1);
    updateCalculatorDisplay(calculatorExpression || "0");
}

function prepareExpression(expression) {
    var preparedExpression = expression;

    while (preparedExpression.indexOf("%") !== -1) {
        preparedExpression = preparedExpression.replace(/(\d+(?:\.\d+)?)%/g, "($1/100)");
    }

    return preparedExpression;
}

function calculateResult() {
    var resultElement = document.getElementById("simpleResult");

    if (!calculatorExpression) {
        resultElement.textContent = "Enter values using the calculator buttons.";
        return;
    }

    try {
        var sanitizedExpression = prepareExpression(calculatorExpression);

        if (!/^[0-9+\-*/().\s]+$/.test(sanitizedExpression)) {
            throw new Error("Invalid expression");
        }

        var result = Function("return " + sanitizedExpression)();

        if (!isFinite(result)) {
            throw new Error("Invalid calculation");
        }

        calculatorExpression = String(parseFloat(result.toFixed(8)));
        updateCalculatorDisplay(calculatorExpression);
        resultElement.textContent = "Result: " + calculatorExpression;
    } catch (error) {
        calculatorExpression = "Error";
        updateCalculatorDisplay("Error");
        resultElement.textContent = "Invalid operation. Please clear and try again.";
    }
}

function calculateEMI() {
    var principal = parseFloat(document.getElementById("loanAmount").value);
    var annualRate = parseFloat(document.getElementById("interestRate").value);
    var tenureYears = parseFloat(document.getElementById("loanTenure").value);
    var resultElement = document.getElementById("emiResult");

    if (isNaN(principal) || isNaN(annualRate) || isNaN(tenureYears)) {
        resultElement.textContent = "Please enter all loan details.";
        return;
    }

    if (principal <= 0 || annualRate < 0 || tenureYears <= 0) {
        resultElement.textContent = "Please enter valid positive values.";
        return;
    }

    var monthlyRate = annualRate / 12 / 100;
    var totalMonths = tenureYears * 12;
    var emi;

    if (monthlyRate === 0) {
        emi = principal / totalMonths;
    } else {
        var ratePower = Math.pow(1 + monthlyRate, totalMonths);
        emi = (principal * monthlyRate * ratePower) / (ratePower - 1);
    }

    var totalPayment = emi * totalMonths;
    var totalInterest = totalPayment - principal;

    resultElement.innerHTML =
        "Monthly EMI: <strong>Rs. " + emi.toFixed(2) + "</strong><br>" +
        "Total Payment: Rs. " + totalPayment.toFixed(2) + "<br>" +
        "Total Interest: Rs. " + totalInterest.toFixed(2);
}

function resetEMICalculator() {
    document.getElementById("loanAmount").value = "";
    document.getElementById("interestRate").value = "";
    document.getElementById("loanTenure").value = "";
    document.getElementById("emiResult").textContent = "Enter loan details to calculate monthly EMI.";
}