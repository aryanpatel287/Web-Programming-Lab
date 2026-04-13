<?php
require_once __DIR__ . '/config.php';

$message = '';
$messageType = '';

function oldValue(string $field): string
{
    return htmlspecialchars($_POST[$field] ?? '');
}

function oldChecked(string $field, string $value): string
{
    return (isset($_POST[$field]) && $_POST[$field] === $value) ? 'checked' : '';
}

function oldSelected(string $field, string $value): string
{
    return (isset($_POST[$field]) && $_POST[$field] === $value) ? 'selected' : '';
}

function oldSkillChecked(string $value): string
{
    $skills = $_POST['skills'] ?? [];
    return (is_array($skills) && in_array($value, $skills, true)) ? 'checked' : '';
}

function getTableColumns(PDO $pdo, string $tableName): array
{
    $statement = $pdo->query("SHOW COLUMNS FROM {$tableName}");
    $columns = [];

    foreach ($statement as $row) {
        $columns[] = $row['Field'];
    }

    return $columns;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $tshirtColor = trim($_POST['tshirt'] ?? '#4a90d9');
    $enrollment = trim($_POST['enroll'] ?? '');
    $branch = trim($_POST['branch'] ?? '');
    $semester = trim($_POST['semester'] ?? '');
    $cgpa = trim($_POST['cgpa'] ?? '');
    $eventName = trim($_POST['event'] ?? '');
    $teamSize = trim($_POST['teamsize'] ?? '');
    $skills = $_POST['skills'] ?? [];
    $portfolio = trim($_POST['portfolio'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $expectations = trim($_POST['expectations'] ?? '');
    $heardFrom = trim($_POST['heardFrom'] ?? '');
    $termsAccepted = isset($_POST['terms']) ? 1 : 0;

    if (
        $fullName === '' || $email === '' || $password === '' || $phone === '' || $dob === '' ||
        $gender === '' || $enrollment === '' || $branch === '' || $semester === '' ||
        $eventName === '' || $heardFrom === '' || $termsAccepted === 0
    ) {
        $message = 'Please fill all required fields and accept the terms.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } elseif (!preg_match('/^[6-9][0-9]{9}$/', $phone)) {
        $message = 'Mobile number must be 10 digits and start with 6, 7, 8, or 9.';
        $messageType = 'error';
    } elseif (!preg_match('/^[0-9]{12}$/', $enrollment)) {
        $message = 'Enrollment number must contain exactly 12 digits.';
        $messageType = 'error';
    } elseif (!in_array($gender, ['male', 'female', 'other'], true)) {
        $message = 'Please select a valid gender.';
        $messageType = 'error';
    } elseif (!ctype_digit($semester) || (int) $semester < 1 || (int) $semester > 8) {
        $message = 'Semester must be between 1 and 8.';
        $messageType = 'error';
    } else {
        $connection = getDatabaseConnection();

        if ($connection['error'] !== '') {
            $message = $connection['error'];
            $messageType = 'error';
        } else {
            $pdo = $connection['pdo'];

            try {
                $tableColumns = getTableColumns($pdo, 'student_event_registrations');
                $allValues = [
                    'full_name' => $fullName,
                    'email' => $email,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    'phone' => $phone,
                    'dob' => $dob,
                    'gender' => $gender,
                    'tshirt_color' => $tshirtColor,
                    'enrollment_number' => $enrollment,
                    'branch' => $branch,
                    'semester' => (int) $semester,
                    'cgpa' => $cgpa !== '' ? (float) $cgpa : null,
                    'event_name' => $eventName,
                    'team_size' => $teamSize !== '' ? (int) $teamSize : null,
                    'skills' => is_array($skills) ? implode(',', $skills) : '',
                    'portfolio_url' => $portfolio,
                    'experience' => $experience,
                    'expectations' => $expectations,
                    'heard_from' => $heardFrom,
                    'terms_accepted' => $termsAccepted,
                    'event_date' => $dob !== '' ? $dob : null,
                ];

                $requiredBaseColumns = ['full_name', 'email', 'event_name'];
                foreach ($requiredBaseColumns as $requiredColumn) {
                    if (!in_array($requiredColumn, $tableColumns, true)) {
                        throw new RuntimeException('Required table column missing: ' . $requiredColumn);
                    }
                }

                $insertColumns = [];
                $params = [];

                foreach ($allValues as $column => $value) {
                    if (in_array($column, $tableColumns, true)) {
                        $insertColumns[] = $column;
                        $params[':' . $column] = $value;
                    }
                }

                if (count($insertColumns) === 0) {
                    throw new RuntimeException('No matching columns found for insert.');
                }

                $columnSql = implode(', ', $insertColumns);
                $placeholderSql = implode(', ', array_map(static fn($column) => ':' . $column, $insertColumns));
                $sql = "INSERT INTO student_event_registrations ({$columnSql}) VALUES ({$placeholderSql})";

                $statement = $pdo->prepare($sql);
                $statement->execute($params);

                $message = 'User details inserted successfully into the database table.';
                $messageType = 'success';
                $_POST = [];
            } catch (Throwable $exception) {
                $message = 'Insert failed: ' . $exception->getMessage();
                $messageType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 12 - Student Event Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="page">
        <section class="panel">
            <h1>Student Event Registration Form</h1>
            <p class="hint">Fill in your details to register for the event. Your registration is stored in the database.</p>

            <?php if ($message !== ''): ?>
                <div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <fieldset>
                    <legend>Personal Information</legend>

                    <label for="fullname">Full Name <span>*</span></label>
                    <input type="text" id="fullname" name="fullname" value="<?php echo oldValue('fullname'); ?>" required>

                    <label for="email">Email Address <span>*</span></label>
                    <input type="email" id="email" name="email" value="<?php echo oldValue('email'); ?>" required>

                    <label for="password">Password <span>*</span></label>
                    <input type="password" id="password" name="password" required>

                    <label for="phone">Mobile Number <span>*</span></label>
                    <input type="text" id="phone" name="phone" value="<?php echo oldValue('phone'); ?>" required>

                    <label for="dob">Date of Birth <span>*</span></label>
                    <input type="date" id="dob" name="dob" value="<?php echo oldValue('dob'); ?>" required>

                    <label>Gender <span>*</span></label>
                    <div class="option-row">
                        <input type="radio" id="male" name="gender" value="male" <?php echo oldChecked('gender', 'male'); ?>>
                        <label for="male">Male</label>

                        <input type="radio" id="female" name="gender" value="female" <?php echo oldChecked('gender', 'female'); ?>>
                        <label for="female">Female</label>

                        <input type="radio" id="other" name="gender" value="other" <?php echo oldChecked('gender', 'other'); ?>>
                        <label for="other">Other</label>
                    </div>

                    <label for="tshirt">Favourite T-Shirt Color</label>
                    <input type="color" id="tshirt" name="tshirt" value="<?php echo oldValue('tshirt') !== '' ? oldValue('tshirt') : '#4a90d9'; ?>">
                </fieldset>

                <fieldset>
                    <legend>Academic Information</legend>

                    <label for="enroll">Enrollment Number <span>*</span></label>
                    <input type="text" id="enroll" name="enroll" value="<?php echo oldValue('enroll'); ?>" required>

                    <label for="branch">Branch <span>*</span></label>
                    <select id="branch" name="branch" required>
                        <option value="">-- Select Branch --</option>
                        <option value="CE" <?php echo oldSelected('branch', 'CE'); ?>>Computer Engineering</option>
                        <option value="EC" <?php echo oldSelected('branch', 'EC'); ?>>Electronics &amp; Communication</option>
                        <option value="ME" <?php echo oldSelected('branch', 'ME'); ?>>Mechanical Engineering</option>
                        <option value="CV" <?php echo oldSelected('branch', 'CV'); ?>>Civil Engineering</option>
                        <option value="EE" <?php echo oldSelected('branch', 'EE'); ?>>Electrical Engineering</option>
                    </select>

                    <label for="semester">Current Semester <span>*</span></label>
                    <input type="number" id="semester" name="semester" min="1" max="8" value="<?php echo oldValue('semester'); ?>" required>

                    <label for="cgpa">CGPA</label>
                    <input type="number" id="cgpa" name="cgpa" min="0" max="10" step="0.01" value="<?php echo oldValue('cgpa'); ?>">
                </fieldset>

                <fieldset>
                    <legend>Event Details</legend>

                    <label for="event">Select Event <span>*</span></label>
                    <select id="event" name="event" required>
                        <option value="">-- Select Event --</option>
                        <optgroup label="Technical">
                            <option value="hackathon" <?php echo oldSelected('event', 'hackathon'); ?>>Hackathon</option>
                            <option value="codingcontest" <?php echo oldSelected('event', 'codingcontest'); ?>>Coding Contest</option>
                            <option value="projectexpo" <?php echo oldSelected('event', 'projectexpo'); ?>>Project Expo</option>
                            <option value="workshop" <?php echo oldSelected('event', 'workshop'); ?>>Workshop</option>
                        </optgroup>
                        <optgroup label="Non-Technical">
                            <option value="culturalnight" <?php echo oldSelected('event', 'culturalnight'); ?>>Cultural Night</option>
                            <option value="sportsday" <?php echo oldSelected('event', 'sportsday'); ?>>Sports Day</option>
                            <option value="debate" <?php echo oldSelected('event', 'debate'); ?>>Debate Competition</option>
                        </optgroup>
                    </select>

                    <label for="teamsize">Team Size</label>
                    <input type="number" id="teamsize" name="teamsize" min="1" max="10" value="<?php echo oldValue('teamsize'); ?>">

                    <label>Skills / Areas of Interest</label>
                    <div class="checkbox-group">
                        <input type="checkbox" id="webdev" name="skills[]" value="webdev" <?php echo oldSkillChecked('webdev'); ?>><label for="webdev">Web Development</label>
                        <input type="checkbox" id="ml" name="skills[]" value="ml" <?php echo oldSkillChecked('ml'); ?>><label for="ml">Machine Learning</label>
                        <input type="checkbox" id="uiux" name="skills[]" value="uiux" <?php echo oldSkillChecked('uiux'); ?>><label for="uiux">UI/UX Design</label>
                        <input type="checkbox" id="android" name="skills[]" value="android" <?php echo oldSkillChecked('android'); ?>><label for="android">Android Development</label>
                        <input type="checkbox" id="cp" name="skills[]" value="cp" <?php echo oldSkillChecked('cp'); ?>><label for="cp">Competitive Programming</label>
                    </div>

                    <label for="portfolio">Portfolio / GitHub URL</label>
                    <input type="url" id="portfolio" name="portfolio" value="<?php echo oldValue('portfolio'); ?>">
                </fieldset>

                <fieldset>
                    <legend>Additional Information</legend>

                    <label for="experience">Previous Event Experience</label>
                    <textarea id="experience" name="experience" rows="4" maxlength="500"><?php echo oldValue('experience'); ?></textarea>

                    <label for="expectations">What do you expect from this event?</label>
                    <textarea id="expectations" name="expectations" rows="4" maxlength="500"><?php echo oldValue('expectations'); ?></textarea>

                    <label for="heardFrom">How did you hear about this event? <span>*</span></label>
                    <select id="heardFrom" name="heardFrom" required>
                        <option value="">-- Select --</option>
                        <option value="social" <?php echo oldSelected('heardFrom', 'social'); ?>>Social Media</option>
                        <option value="friend" <?php echo oldSelected('heardFrom', 'friend'); ?>>Friend / Classmate</option>
                        <option value="notice" <?php echo oldSelected('heardFrom', 'notice'); ?>>College Notice Board</option>
                        <option value="faculty" <?php echo oldSelected('heardFrom', 'faculty'); ?>>Faculty</option>
                        <option value="other" <?php echo oldSelected('heardFrom', 'other'); ?>>Other</option>
                    </select>

                    <div class="terms-row">
                        <input type="checkbox" id="terms" name="terms" <?php echo isset($_POST['terms']) ? 'checked' : ''; ?>>
                        <label for="terms">I agree to the terms and conditions of the event. <span>*</span></label>
                    </div>
                </fieldset>

                <button type="submit">Insert User Details</button>
            </form>
        </section>
    </main>
</body>
</html>
