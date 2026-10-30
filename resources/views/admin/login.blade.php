<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Login - Anandam Sweets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        saffron: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                            950: '#451a03',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-saffron-950 via-stone-900 to-saffron-900 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl border border-saffron-200/50 p-8 space-y-6">
        
        <div class="text-center space-y-2">
            <div class="w-16 h-16 rounded-full bg-saffron-600 text-white flex items-center justify-center font-serif font-black text-2xl mx-auto shadow-lg border-2 border-amber-300">
                AS
            </div>
            <h1 class="text-2xl font-serif font-bold text-saffron-950">Anandam Sweets</h1>
            <p class="text-xs text-stone-500 uppercase tracking-widest font-semibold">Store Management Portal</p>
        </div>

        @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="font-bold text-stone-700 block mb-1">Staff / Owner Email</label>
                <input 
                    type="email" 
                    name="email" 
                    required 
                    value="{{ old('email', 'admin@anandamsweets.in') }}" 
                    placeholder="admin@anandamsweets.in"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-saffron-600/30 focus:border-saffron-600 text-sm"
                />
            </div>

            <div>
                <label class="font-bold text-stone-700 block mb-1">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    value="admin123"
                    placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 focus:outline-none focus:ring-2 focus:ring-saffron-600/30 focus:border-saffron-600 text-sm"
                />
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-stone-600">
                    <input type="checkbox" name="remember" class="rounded text-saffron-600 focus:ring-saffron-500" checked />
                    <span>Remember this device</span>
                </label>
            </div>

            <button 
                type="submit" 
                class="w-full py-3 rounded-xl bg-gradient-to-r from-saffron-600 to-saffron-700 hover:from-saffron-700 hover:to-saffron-800 text-white font-serif font-bold text-sm shadow-md transition-all active:scale-[0.98]"
            >
                Sign In to Admin Panel
            </button>
        </form>

        <div class="pt-4 border-t border-stone-100 text-center text-[11px] text-stone-400">
            Authorized Personnel Only • IP Logged & Protected
        </div>

    </div>

</body>
</html>
