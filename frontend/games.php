<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Game Pad — vomp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        html,body{height:100%}
        body{
            font-family:Inter,system-ui,sans-serif;
            background:linear-gradient(180deg,#0b0d0f,#0d0f10,#0a0c0d);
            color:#e7edf2;
            display:flex;
            align-items:center;
            justify-content:center;
            min-height:100vh;
            padding:20px;
        }
        .card{
            background:#121518;
            border:1px solid #2a2f36;
            border-radius:2.5rem;
            padding:60px 48px;
            text-align:center;
            max-width:420px;
            width:100%;
            box-shadow:0 20px 60px rgba(0,0,0,.5);
        }
        .icon{
            font-size:3rem;
            margin-bottom:20px;
            display:block;
        }
        h1{
            font-size:1.75rem;
            font-weight:900;
            color:#fff;
            margin-bottom:8px;
        }
        p{
            color:#98a2ad;
            font-size:0.95rem;
            line-height:1.6;
            margin-bottom:32px;
        }
        .btn{
            display:inline-block;
            padding:14px 40px;
            border-radius:14px;
            background:#ff6a00;
            color:#111;
            font-weight:800;
            font-size:0.95rem;
            text-decoration:none;
            transition:all .2s;
        }
        .btn:hover{background:#ff8533;transform:translateY(-1px)}
    </style>
</head>
<body>
    <div class="card">
        <span class="icon">🎮</span>
        <h1>Coming Soon</h1>
        <p>We're building something exciting. Game Pad will be available soon — stay tuned!</p>
        <a href="/dashboard" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>
