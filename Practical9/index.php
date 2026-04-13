<?php
$basicPay = '';
$daPercent = '';
$hraPercent = '';
$salarySlip = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $basicPay = isset($_POST['basic_pay']) ? (float) $_POST['basic_pay'] : 0;
    $daPercent = isset($_POST['da_percent']) ? (float) $_POST['da_percent'] : 0;
    $hraPercent = isset($_POST['hra_percent']) ? (float) $_POST['hra_percent'] : 0;

    if ($basicPay <= 0 || $daPercent < 0 || $hraPercent < 0) {
        $error = 'Please enter a valid Basic Pay and non-negative percentage values.';
    } else {
        $daAmount = ($basicPay * $daPercent) / 100;
        $hraAmount = ($basicPay * $hraPercent) / 100;
        $grossSalary = $basicPay + $daAmount + $hraAmount;
        $pfDeduction = ($basicPay * 12) / 100;
        $netSalary = $grossSalary - $pfDeduction;

        $salarySlip = [
            'basicPay' => $basicPay,
            'daPercent' => $daPercent,
            'daAmount' => $daAmount,
            'hraPercent' => $hraPercent,
            'hraAmount' => $hraAmount,
            'grossSalary' => $grossSalary,
            'pfDeduction' => $pfDeduction,
            'netSalary' => $netSalary,
        ];
    }
}

function moneyFormat($amount) {
    return 'Rs. ' . number_format($amount, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 9 - Salary Slip</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="page">
        <h1>Salary Slip</h1>
        <div class="layout">
            <section class="panel">
                <h2>Enter Salary Details</h2>
                <form method="post" action="">
                    <label for="basic_pay">Basic Pay</label>
                    <input type="number" id="basic_pay" name="basic_pay" step="0.01" min="1" value="<?php echo htmlspecialchars((string) $basicPay); ?>" required>

                    <label for="da_percent">DA Percentage</label>
                    <input type="number" id="da_percent" name="da_percent" step="0.01" min="0" value="<?php echo htmlspecialchars((string) $daPercent); ?>" required>

                    <label for="hra_percent">HRA Percentage</label>
                    <input type="number" id="hra_percent" name="hra_percent" step="0.01" min="0" value="<?php echo htmlspecialchars((string) $hraPercent); ?>" required>

                    <button type="submit">Generate Salary Slip</button>
                </form>

                <?php if ($error !== ''): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
            </section>

            <section class="panel">
                <h2>Salary Calculation</h2>
                <?php if ($salarySlip !== null): ?>
                    <table>
                        <tr>
                            <th>Particular</th>
                            <th>Amount</th>
                        </tr>
                        <tr>
                            <td>Basic Pay</td>
                            <td><?php echo moneyFormat($salarySlip['basicPay']); ?></td>
                        </tr>
                        <tr>
                            <td>DA (<?php echo $salarySlip['daPercent']; ?>%)</td>
                            <td><?php echo moneyFormat($salarySlip['daAmount']); ?></td>
                        </tr>
                        <tr>
                            <td>HRA (<?php echo $salarySlip['hraPercent']; ?>%)</td>
                            <td><?php echo moneyFormat($salarySlip['hraAmount']); ?></td>
                        </tr>
                        <tr class="total-row">
                            <td>Gross Salary</td>
                            <td><?php echo moneyFormat($salarySlip['grossSalary']); ?></td>
                        </tr>
                        <tr>
                            <td>PF Deduction (12% of Basic Pay)</td>
                            <td><?php echo moneyFormat($salarySlip['pfDeduction']); ?></td>
                        </tr>
                        <tr class="total-row">
                            <td>Net Salary</td>
                            <td><?php echo moneyFormat($salarySlip['netSalary']); ?></td>
                        </tr>
                    </table>
                    <p class="note">Net Salary = Gross Salary - PF Deduction</p>
                <?php else: ?>
                    <p>Fill the form and submit it to generate the salary slip.</p>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>
