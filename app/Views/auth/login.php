<!DOCTYPE html>
<html lang="id" class=""><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login - SITATIB</title><script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={darkMode:'class'}</script></head>
<body class="min-h-screen grid place-items-center px-4 py-8 bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100">
<form method="post" action="/login" class="w-full max-w-sm p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow">
  <h1 class="text-xl font-bold">SITATIB Login</h1>
  <p class="text-xs text-slate-500 mb-4">SMK Negeri 1 Krangkeng • admin/kepsek/wakasek/wali/osis</p>
  <?php if (!empty($error)): ?><div class="text-sm p-2 mb-3 rounded bg-red-100 text-red-700"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <label class="text-sm">Username<input name="username" required class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
  <label class="text-sm block mt-3">Password<input type="password" name="password" required class="mt-1 w-full p-2 rounded-lg border dark:bg-slate-800 dark:border-slate-700"></label>
  <button class="mt-4 w-full py-2 rounded-lg bg-sky-600 text-white">Masuk</button>
</form></body></html>
