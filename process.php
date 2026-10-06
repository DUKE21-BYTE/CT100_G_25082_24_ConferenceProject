<?php
// Define base charges
$REGISTRATION_FEE = 1000;
$ATTENDANCE_PER_DAY = 500;
$WORKSHOP_1_FEE = 750;
$WORKSHOP_2_FEE = 1000;
$MEAL_PER_DAY = 300;

$errors = [];
$totalPayable = 0;

// Function to sanitize input
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Receive and sanitize values
    $fullName = isset($_POST['fullName']) ? clean_input($_POST['fullName']) : '';
    $admissionNumber = isset($_POST['admissionNumber']) ? clean_input($_POST['admissionNumber']) : '';
    $email = isset($_POST['email']) ? clean_input($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? clean_input($_POST['phone']) : '';
    $yearOfStudy = isset($_POST['yearOfStudy']) ? clean_input($_POST['yearOfStudy']) : '';
    $conferenceDays = isset($_POST['conferenceDays']) ? (int)$_POST['conferenceDays'] : 0;
    $participationCategory = isset($_POST['participationCategory']) ? clean_input($_POST['participationCategory']) : '';
    
    $workshop1 = isset($_POST['workshop1']) ? clean_input($_POST['workshop1']) : '';
    $workshop2 = isset($_POST['workshop2']) ? clean_input($_POST['workshop2']) : '';
    $catering = isset($_POST['catering']) ? clean_input($_POST['catering']) : 'No';

    // Server-side validation
    if (empty($fullName)) {
        $errors[] = "Full name is required.";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    if (empty($phone) || !preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Phone number must be 10 digits.";
    }
    
    if ($conferenceDays < 1 || $conferenceDays > 3) {
        $errors[] = "Conference days must be between 1 and 3.";
    }
    
    if (empty($participationCategory)) {
        $errors[] = "Participation category is required.";
    }
    
    // If validation passes, calculate fees
    if (empty($errors)) {
        // Attendance Fee
        $attendanceFee = $conferenceDays * $ATTENDANCE_PER_DAY;
        
        // Workshop Fee
        $workshopFee = 0;
        $workshopsSelected = [];
        if ($participationCategory === 'Workshop Participant') {
            if ($workshop1) {
                $workshopFee += $WORKSHOP_1_FEE;
                $workshopsSelected[] = $workshop1;
            }
            if ($workshop2) {
                $workshopFee += $WORKSHOP_2_FEE;
                $workshopsSelected[] = $workshop2;
            }
        }
        $workshopString = !empty($workshopsSelected) ? implode(", ", $workshopsSelected) : "None";
        
        // Meal Fee
        $mealFee = 0;
        if ($catering === 'Yes') {
            $mealFee = $conferenceDays * $MEAL_PER_DAY;
        }
        
        // Total Calculate
        $totalPayable = $REGISTRATION_FEE + $attendanceFee + $workshopFee + $mealFee;
    }
} else {
    // Redirect to register if not accessed via POST
    header("Location: register.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Confirmation - TechConf 2026</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo-container">
                <img src="images/Kirinyaga University Crest Emblem.png" alt="University Emblem">
                <div class="logo">TechConf 2026</div>
            </div>
            <div class="nav-links">
                <a href="index.html">Home</a>
                <a href="register.html">Register</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="form-container" style="max-width: 600px; text-align: center;">
            <?php if (!empty($errors)): ?>
                <h2 style="color: #cc0000; margin-bottom: 1rem;">Registration Failed</h2>
                <div style="background: #ffe6e6; padding: 1.5rem; border-radius: 8px; border: 1px solid #cc0000; text-align: left;">
                    <ul>
                        <?php foreach($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <br>
                <a href="register.html" class="btn">Go Back to Registration</a>
            <?php else: ?>
                <h2 style="color: #008000; margin-bottom: 0.5rem;">REGISTRATION SUCCESSFUL</h2>
                <p style="color: #666; margin-bottom: 2rem;">Thank you for registering for the Student Technology and Innovation Conference.</p>
                
                <div style="text-align: left; background: #f9f9f9; padding: 2rem; border-radius: 8px; border: 1px solid #ccc; color: #333;">
                    <p style="margin-bottom: 0.5rem;"><strong>Participant:</strong> <?php echo $fullName; ?></p>
                    <p style="margin-bottom: 0.5rem;"><strong>Admission Number:</strong> <?php echo $admissionNumber; ?></p>
                    <p style="margin-bottom: 0.5rem;"><strong>Email:</strong> <?php echo $email; ?></p>
                    <p style="margin-bottom: 0.5rem;"><strong>Conference Days:</strong> <?php echo $conferenceDays; ?></p>
                    <p style="margin-bottom: 0.5rem;"><strong>Workshop:</strong> <?php echo $workshopString; ?></p>
                    <p style="margin-bottom: 1.5rem;"><strong>Meals:</strong> <?php echo $catering; ?></p>
                    
                    <div style="border-top: 2px solid #ccc; padding-top: 1.5rem; margin-top: 1rem;">
                        <h3 style="color: #003366; display: flex; justify-content: space-between;">
                            <span>TOTAL AMOUNT PAYABLE:</span>
                            <span>KSh <?php echo number_format($totalPayable); ?></span>
                        </h3>
                    </div>
                </div>
                
                <div style="margin-top: 2rem;">
                    <a href="index.html" class="btn">Back to Home</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
