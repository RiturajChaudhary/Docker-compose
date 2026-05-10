<?php
require_once 'config.php';

$message = '';
$messageType = '';
$editData = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name       = trim($_POST['name'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $position   = trim($_POST['position'] ?? '');
        $salary     = trim($_POST['salary'] ?? '');

        if ($name && $email && $department && $position && $salary) {
            $conn = getConnection();
            $stmt = $conn->prepare("INSERT INTO employees (name, email, department, position, salary) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssd", $name, $email, $department, $position, $salary);
            if ($stmt->execute()) {
                $message = "Employee <strong>$name</strong> added successfully!";
                $messageType = 'success';
            } else {
                $message = "Error: " . $conn->error;
                $messageType = 'error';
            }
            $stmt->close();
            $conn->close();
        } else {
            $message = "All fields are required.";
            $messageType = 'error';
        }
    }

    if ($action === 'update') {
        $id         = (int)($_POST['id'] ?? 0);
        $name       = trim($_POST['name'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $position   = trim($_POST['position'] ?? '');
        $salary     = trim($_POST['salary'] ?? '');

        if ($id && $name && $email && $department && $position && $salary) {
            $conn = getConnection();
            $stmt = $conn->prepare("UPDATE employees SET name=?, email=?, department=?, position=?, salary=? WHERE id=?");
            $stmt->bind_param("ssssdi", $name, $email, $department, $position, $salary, $id);
            if ($stmt->execute()) {
                $message = "Employee <strong>$name</strong> updated successfully!";
                $messageType = 'success';
            } else {
                $message = "Error: " . $conn->error;
                $messageType = 'error';
            }
            $stmt->close();
            $conn->close();
        } else {
            $message = "All fields are required.";
            $messageType = 'error';
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $conn = getConnection();
            $stmt = $conn->prepare("DELETE FROM employees WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = "Employee deleted successfully.";
                $messageType = 'success';
            } else {
                $message = "Error: " . $conn->error;
                $messageType = 'error';
            }
            $stmt->close();
            $conn->close();
        }
    }
}

// Handle edit GET request
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM employees WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $editData = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
}

// Fetch all employees
$conn = getConnection();
$result = $conn->query("SELECT * FROM employees ORDER BY created_at DESC");
$employees = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee CRUD App</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:       #0d0f14;
            --surface:  #161921;
            --border:   #252832;
            --accent:   #6ee7b7;
            --accent2:  #f472b6;
            --text:     #e8eaf0;
            --muted:    #6b7280;
            --danger:   #f87171;
            --success:  #6ee7b7;
            --radius:   10px;
        }

        body {
            font-family: 'Syne', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        /* ── Header ── */
        header {
            max-width: 1100px;
            margin: 0 auto 2.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        header .logo {
            width: 44px; height: 44px;
            background: var(--accent);
            border-radius: 10px;
            display: grid; place-items: center;
            font-size: 1.4rem;
        }
        header h1 { font-size: 1.6rem; font-weight: 800; letter-spacing: -.5px; }
        header span { color: var(--accent); }

        /* ── Layout ── */
        .container {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 2rem;
            align-items: start;
        }
        @media (max-width: 800px) {
            .container { grid-template-columns: 1fr; }
        }

        /* ── Card ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.75rem;
        }
        .card h2 {
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--muted);
            margin-bottom: 1.5rem;
        }

        /* ── Form ── */
        .form-group { margin-bottom: 1rem; }
        .form-group label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            margin-bottom: .4rem;
        }
        .form-group input {
            width: 100%;
            padding: .65rem .85rem;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--text);
            font-family: 'DM Mono', monospace;
            font-size: .9rem;
            transition: border-color .2s;
            outline: none;
        }
        .form-group input:focus { border-color: var(--accent); }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .6rem 1.2rem;
            border-radius: 6px;
            font-family: 'Syne', sans-serif;
            font-size: .85rem;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: opacity .2s, transform .1s;
            text-decoration: none;
        }
        .btn:active { transform: scale(.97); }
        .btn-primary  { background: var(--accent);  color: #0d0f14; width: 100%; justify-content: center; margin-top: .5rem; }
        .btn-edit     { background: #1e2535; color: var(--accent); border: 1px solid var(--border); padding: .35rem .75rem; font-size: .78rem; }
        .btn-delete   { background: #2a1a1a; color: var(--danger); border: 1px solid #3a1f1f;     padding: .35rem .75rem; font-size: .78rem; }
        .btn-cancel   { background: #1e2535; color: var(--text);   border: 1px solid var(--border); width: 100%; justify-content: center; margin-top: .4rem; }
        .btn:hover    { opacity: .85; }

        /* ── Alert ── */
        .alert {
            max-width: 1100px;
            margin: 0 auto 1.5rem;
            padding: .9rem 1.2rem;
            border-radius: var(--radius);
            font-size: .9rem;
            font-weight: 600;
        }
        .alert-success { background: #0d2e22; border: 1px solid #166534; color: var(--success); }
        .alert-error   { background: #2a1010; border: 1px solid #7f1d1d; color: var(--danger); }

        /* ── Table ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .88rem; }
        thead tr { border-bottom: 2px solid var(--border); }
        thead th {
            padding: .65rem 1rem;
            text-align: left;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--muted);
            white-space: nowrap;
        }
        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #1a1d26; }
        tbody td { padding: .8rem 1rem; vertical-align: middle; }

        .badge {
            display: inline-block;
            padding: .25rem .65rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
            background: #1a2535;
            color: var(--accent);
            border: 1px solid #1e3a5f;
            font-family: 'DM Mono', monospace;
        }
        .salary { font-family: 'DM Mono', monospace; color: var(--accent2); }
        .actions { display: flex; gap: .5rem; }

        .empty {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--muted);
        }
        .empty .icon { font-size: 2.5rem; margin-bottom: .75rem; }

        .count-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .count-bar h2 { margin-bottom: 0; }
        .count-pill {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: .2rem .75rem;
            font-size: .8rem;
            font-family: 'DM Mono', monospace;
            color: var(--accent);
        }

        /* editing highlight */
        .card.editing { border-color: var(--accent2); }
        .card.editing h2 { color: var(--accent2); }
    </style>
</head>
<body>

<header>
    <div class="logo">🗂</div>
    <h1>Employee <span>Manager</span></h1>
</header>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType === 'success' ? 'success' : 'error' ?>">
    <?= $message ?>
</div>
<?php endif; ?>

<div class="container">

    <!-- ── FORM PANEL ── -->
    <div class="card <?= $editData ? 'editing' : '' ?>">
        <h2><?= $editData ? '✏ Edit Employee' : '+ New Employee' ?></h2>
        <form method="POST" action="index.php">
            <input type="hidden" name="action" value="<?= $editData ? 'update' : 'create' ?>">
            <?php if ($editData): ?>
                <input type="hidden" name="id" value="<?= $editData['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="e.g. Jane Doe" required
                       value="<?= htmlspecialchars($editData['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="jane@company.com" required
                       value="<?= htmlspecialchars($editData['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Department</label>
                <input type="text" name="department" placeholder="e.g. Engineering" required
                       value="<?= htmlspecialchars($editData['department'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Position</label>
                <input type="text" name="position" placeholder="e.g. Senior Developer" required
                       value="<?= htmlspecialchars($editData['position'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Salary (USD)</label>
                <input type="number" name="salary" placeholder="e.g. 75000" step="0.01" min="0" required
                       value="<?= htmlspecialchars($editData['salary'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-primary">
                <?= $editData ? '💾 Save Changes' : '＋ Add Employee' ?>
            </button>
            <?php if ($editData): ?>
                <a href="index.php" class="btn btn-cancel">✕ Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- ── TABLE PANEL ── -->
    <div class="card">
        <div class="count-bar">
            <h2>All Employees</h2>
            <span class="count-pill"><?= count($employees) ?> records</span>
        </div>

        <?php if (empty($employees)): ?>
            <div class="empty">
                <div class="icon">📭</div>
                <p>No employees yet. Add one!</p>
            </div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Salary</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><span style="color:var(--muted);font-family:'DM Mono',monospace;font-size:.8rem"><?= $emp['id'] ?></span></td>
                        <td><strong><?= htmlspecialchars($emp['name']) ?></strong></td>
                        <td style="color:var(--muted);font-family:'DM Mono',monospace;font-size:.82rem"><?= htmlspecialchars($emp['email']) ?></td>
                        <td><span class="badge"><?= htmlspecialchars($emp['department']) ?></span></td>
                        <td><?= htmlspecialchars($emp['position']) ?></td>
                        <td class="salary">$<?= number_format($emp['salary'], 2) ?></td>
                        <td>
                            <div class="actions">
                                <a href="index.php?edit=<?= $emp['id'] ?>" class="btn btn-edit">✏ Edit</a>
                                <form method="POST" action="index.php"
                                      onsubmit="return confirm('Delete <?= htmlspecialchars($emp['name']) ?>?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $emp['id'] ?>">
                                    <button type="submit" class="btn btn-delete">🗑 Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
