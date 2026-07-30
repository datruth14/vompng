<?php
$pageTitle = 'Admin Email - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-8">
    <header>
        <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Super Admin / Email</p>
        <h1 class="text-5xl font-black text-white tracking-tight">Send Email</h1>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Recipient Filter</label>
                <select id="filterSelect" onchange="onFilterChange()" class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white focus:outline-none focus:border-[#ff610a]/50 transition-all text-sm">
                    <option value="" class="bg-gray-900">— Select Filter —</option>
                    <option value="all" class="bg-gray-900">All Registered Users</option>
                    <option value="with_stores" class="bg-gray-900">Users with Stores</option>
                    <option value="without_stores" class="bg-gray-900">Users without Stores</option>
                    <option value="min_2_products" class="bg-gray-900">Users with 2+ Products</option>
                    <option value="min_5_products" class="bg-gray-900">Users with 5+ Products</option>
                    <option value="single" class="bg-gray-900">Single User</option>
                </select>
            </div>

            <div id="singleSearchWrap" class="hidden">
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Search User</label>
                <input type="text" id="singleSearch" placeholder="Type name or email..." oninput="searchSingleUser()" class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 transition-all text-sm">
            </div>

            <div id="recipientCount" class="text-sm text-gray-400 font-medium hidden"></div>

            <div class="overflow-x-auto max-h-80 overflow-y-auto glass-morphism rounded-2xl border border-white/10">
                <table class="w-full text-sm" id="userTable">
                    <thead>
                        <tr class="text-gray-500 uppercase tracking-wider text-xs font-black">
                            <th class="p-3 w-10"><input type="checkbox" id="selectAll" onchange="toggleAll()" class="rounded bg-white/5 border-white/20 text-[#ff610a] focus:ring-[#ff610a]"></th>
                            <th class="text-left p-3">Name</th>
                            <th class="text-left p-3">Email</th>
                        </tr>
                    </thead>
                    <tbody id="userList"></tbody>
                </table>
                <div id="loadingUsers" class="text-center py-8 text-gray-500 text-sm">Select a filter to load users</div>
            </div>

            <div class="flex items-center gap-2 text-sm text-gray-400 font-medium">
                <span id="selectedCount">0</span> user(s) selected
            </div>
        </div>

        <div class="space-y-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Subject</label>
                <input type="text" id="subject" placeholder="Email subject..." class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 transition-all text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Message</label>
                <textarea id="message" rows="10" placeholder="Write your message here..." class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 transition-all text-sm resize-y"></textarea>
            </div>

            <div id="sendProgress" class="hidden">
                <div class="w-full bg-white/5 rounded-full h-2 overflow-hidden">
                    <div id="sendProgressBar" class="bg-[#ff610a] h-full rounded-full transition-all duration-300" style="width:0%"></div>
                </div>
                <p id="sendStatus" class="text-sm text-gray-400 mt-2"></p>
            </div>

            <div id="sendErrors" class="text-sm text-red-400 space-y-1 hidden"></div>
            <div id="sendSuccess" class="text-sm text-emerald-400 font-bold hidden"></div>

            <div class="flex gap-3">
                <button onclick="sendEmail()" id="sendBtn" class="flex-1 px-6 py-3 rounded-2xl bg-[#ff610a] text-white font-bold text-sm shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                    Send Email
                </button>
            </div>
        </div>
    </div>
</section>

<script>
let allUsers = [];
let searchTimeout;

function onFilterChange() {
    const filter = document.getElementById('filterSelect').value;
    const singleWrap = document.getElementById('singleSearchWrap');
    if (!filter) {
        singleWrap.classList.add('hidden');
        document.getElementById('selectAll').classList.add('hidden');
        document.getElementById('userList').innerHTML = '';
        document.getElementById('recipientCount').classList.add('hidden');
        document.getElementById('loadingUsers').textContent = 'Select a filter to load users';
        document.getElementById('loadingUsers').classList.remove('hidden');
        updateCount();
        return;
    }
    if (filter === 'single') {
        singleWrap.classList.remove('hidden');
        document.getElementById('recipientCount').classList.add('hidden');
        document.getElementById('userList').innerHTML = '';
        document.getElementById('loadingUsers').textContent = 'Type at least 2 characters to search';
        document.getElementById('loadingUsers').classList.remove('hidden');
        document.getElementById('selectAll').classList.add('hidden');
        updateCount();
        return;
    }
    singleWrap.classList.add('hidden');
    document.getElementById('selectAll').classList.remove('hidden');
    loadRecipients();
}

function searchSingleUser() {
    clearTimeout(searchTimeout);
    const q = document.getElementById('singleSearch').value.trim();
    if (q.length < 2) {
        document.getElementById('userList').innerHTML = '';
        document.getElementById('loadingUsers').textContent = 'Type at least 2 characters to search';
        document.getElementById('loadingUsers').classList.remove('hidden');
        document.getElementById('recipientCount').classList.add('hidden');
        return;
    }
    document.getElementById('loadingUsers').textContent = 'Searching...';
    document.getElementById('loadingUsers').classList.remove('hidden');

    fetch('/api/admin/send_email?action=list&filter=single&q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderUsers(data.users);
                document.getElementById('recipientCount').textContent = data.users.length + ' user(s) found';
                document.getElementById('recipientCount').classList.remove('hidden');
                document.getElementById('loadingUsers').classList.add('hidden');
            }
        });
}

function loadRecipients() {
    const filter = document.getElementById('filterSelect').value;
    document.getElementById('loadingUsers').textContent = 'Loading...';

    fetch('/api/admin/send_email?action=list&filter=' + filter)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                allUsers = data.users;
                renderUsers(allUsers);
                document.getElementById('recipientCount').textContent = allUsers.length + ' user(s) found';
                document.getElementById('recipientCount').classList.remove('hidden');
                document.getElementById('loadingUsers').classList.add('hidden');
            } else {
                document.getElementById('loadingUsers').textContent = 'Failed to load users';
            }
        })
        .catch(() => {
            document.getElementById('loadingUsers').textContent = 'Network error';
        });
}

function renderUsers(users) {
    const tbody = document.getElementById('userList');
    tbody.innerHTML = '';
    users.forEach(u => {
        const tr = document.createElement('tr');
        tr.className = 'border-t border-white/5 hover:bg-white/[0.02]';
        tr.innerHTML = '<td class="p-3"><input type="checkbox" value="' + u.email + '" onchange="updateCount()" class="user-cb rounded bg-white/5 border-white/20 text-[#ff610a] focus:ring-[#ff610a]"></td>' +
            '<td class="p-3 text-white font-bold">' + escapeHtml(u.name) + '</td>' +
            '<td class="p-3 text-gray-400">' + escapeHtml(u.email) + '</td>';
        tbody.appendChild(tr);
    });
    document.getElementById('selectAll').checked = false;
    updateCount();
}

function toggleAll() {
    const checked = document.getElementById('selectAll').checked;
    document.querySelectorAll('.user-cb').forEach(cb => cb.checked = checked);
    updateCount();
}

function updateCount() {
    const count = document.querySelectorAll('.user-cb:checked').length;
    document.getElementById('selectedCount').textContent = count;
}

function escapeHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

async function sendEmail() {
    const subject = document.getElementById('subject').value.trim();
    const message = document.getElementById('message').value.trim();
    const recipients = Array.from(document.querySelectorAll('.user-cb:checked')).map(cb => cb.value);

    if (!recipients.length) { alert('Select at least one recipient.'); return; }
    if (!subject) { alert('Enter a subject.'); return; }
    if (!message) { alert('Enter a message.'); return; }

    if (!confirm('Send email to ' + recipients.length + ' recipient(s)?')) return;

    const btn = document.getElementById('sendBtn');
    const progress = document.getElementById('sendProgress');
    const progressBar = document.getElementById('sendProgressBar');
    const statusEl = document.getElementById('sendStatus');
    const errorsEl = document.getElementById('sendErrors');
    const successEl = document.getElementById('sendSuccess');

    btn.disabled = true;
    btn.textContent = 'Sending...';
    progress.classList.remove('hidden');
    errorsEl.classList.add('hidden');
    successEl.classList.add('hidden');

    try {
        const res = await fetch('/api/admin/send_email', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ recipients, subject, message })
        });
        const data = await res.json();

        progressBar.style.width = '100%';

        if (data.success) {
            if (data.errors && data.errors.length) {
                errorsEl.innerHTML = data.errors.map(e => '<div>&bull; ' + escapeHtml(e) + '</div>').join('');
                errorsEl.classList.remove('hidden');
            }
            successEl.textContent = data.sent + '/' + data.total + ' email(s) sent successfully';
            successEl.classList.remove('hidden');
        } else {
            alert(data.error || 'Failed to send');
        }
    } catch (e) {
        alert('Network error. Please try again.');
    }

    statusEl.textContent = 'Done';
    btn.disabled = false;
    btn.textContent = 'Send Email';
}
</script>

<?php
$content = ob_get_clean();
?>
