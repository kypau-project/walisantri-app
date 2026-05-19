<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $exam->title }} — Ujian Online</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #0D9488;
            --primary-dark: #0F766E;
            --primary-50: #F0FDFA;
            --success: #15803D;
            --danger: #DC2626;
            --warning: #D97706;
            --text: #1E293B;
            --text-secondary: #64748B;
            --text-muted: #94A3B8;
            --border: #E2E8F0;
            --bg: #F8FAFC;
            --font: 'Plus Jakarta Sans', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            user-select: none;
            -webkit-user-select: none;
        }

        /* Top Bar */
        .exam-topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .exam-title { font-size: 14px; font-weight: 700; color: var(--text); }
        .exam-subject { font-size: 11px; color: var(--text-secondary); }

        .timer-box {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 18px;
            transition: all 0.3s;
        }

        .timer-box.safe { background: #DCFCE7; color: #15803D; }
        .timer-box.warning { background: #FEF3C7; color: #D97706; }
        .timer-box.danger { background: #FEE2E2; color: #DC2626; animation: pulse 1s infinite; }

        @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.7; } }

        /* Content */
        .exam-content {
            margin-top: 70px;
            padding: 20px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            padding-bottom: 100px;
        }

        /* Question Card */
        .question-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .question-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .question-number {
            background: var(--primary);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            flex-shrink: 0;
        }

        .question-meta {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .question-badge {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 600;
        }

        .question-badge.mc { background: var(--primary-50); color: var(--primary-dark); }
        .question-badge.essay { background: #FEF3C7; color: var(--warning); }
        .question-points { font-size: 11px; color: var(--text-muted); }

        .question-text {
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 16px;
            white-space: pre-wrap;
        }

        /* MC Options */
        .mc-options { display: grid; gap: 8px; }
        .mc-option {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }

        .mc-option:hover { border-color: var(--primary); background: var(--primary-50); }
        .mc-option.selected { border-color: var(--primary); background: var(--primary-50); }

        .mc-option-key {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
            transition: all 0.2s;
        }

        .mc-option.selected .mc-option-key {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .mc-option-text { flex: 1; padding-top: 3px; }

        /* Essay */
        .essay-textarea {
            width: 100%;
            min-height: 120px;
            padding: 14px;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-family: var(--font);
            font-size: 14px;
            resize: vertical;
            outline: none;
            transition: border-color 0.2s;
        }

        .essay-textarea:focus { border-color: var(--primary); }

        /* Save indicator */
        .save-indicator {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 6px;
            text-align: right;
        }

        .save-indicator.saving { color: var(--warning); }
        .save-indicator.saved { color: var(--success); }

        /* Bottom Bar */
        .exam-bottombar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid var(--border);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.06);
            z-index: 100;
        }

        .progress-text { font-size: 12px; color: var(--text-secondary); }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            font-family: var(--font);
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-submit {
            background: var(--primary);
            color: white;
        }

        .btn-submit:hover { background: var(--primary-dark); }

        /* Question Navigator */
        .question-nav {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .nav-dot {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-muted);
        }

        .nav-dot.answered { background: var(--primary); color: white; border-color: var(--primary); }
        .nav-dot:hover { border-color: var(--primary); }


        @media (max-width: 600px) {
            .exam-topbar { padding: 8px 12px; }
            .timer-box { font-size: 15px; padding: 6px 12px; }
            .exam-content { padding: 12px; margin-top: 60px; }
            .question-card { padding: 16px; }
        }
    </style>
</head>
<body>

    <!-- Top Bar -->
    <div class="exam-topbar">
        <div>
            <div class="exam-title">{{ $exam->title }}</div>
            <div class="exam-subject">{{ $exam->subject }} · {{ $exam->duration_minutes }} menit</div>
        </div>
        <div class="timer-box safe" id="timerBox">
            <i class="fas fa-clock"></i>
            <span id="timerDisplay">--:--</span>
        </div>
    </div>

    <!-- Content -->
    <div class="exam-content">
        @foreach($questions as $i => $q)
        <div class="question-card" id="question-{{ $q->id }}">
            <div class="question-header">
                <div class="question-number">{{ $i + 1 }}</div>
                <div class="question-meta">
                    <span class="question-badge {{ $q->question_type === 'multiple_choice' ? 'mc' : 'essay' }}">
                        {{ $q->question_type === 'multiple_choice' ? 'Pilihan Ganda' : 'Essay' }}
                    </span>
                    <span class="question-points">{{ $q->points }} poin</span>
                </div>
            </div>

            <div class="question-text">{{ $q->question_text }}</div>

            @if($q->question_type === 'multiple_choice')
            <div class="mc-options">
                @foreach($q->options ?? [] as $opt)
                <div class="mc-option {{ ($existingAnswers[$q->id] ?? '') === $opt['key'] ? 'selected' : '' }}"
                     onclick="selectOption({{ $q->id }}, '{{ $opt['key'] }}', this)"
                     data-question="{{ $q->id }}" data-key="{{ $opt['key'] }}">
                    <div class="mc-option-key">{{ $opt['key'] }}</div>
                    <div class="mc-option-text">{{ $opt['text'] }}</div>
                </div>
                @endforeach
            </div>
            @else
            <textarea class="essay-textarea" placeholder="Tulis jawaban Anda di sini..."
                      data-question="{{ $q->id }}"
                      onblur="saveEssay({{ $q->id }}, this.value)"
                      oninput="markUnsaved({{ $q->id }})">{{ $existingAnswers[$q->id] ?? '' }}</textarea>
            @endif

            <div class="save-indicator" id="save-{{ $q->id }}"></div>
        </div>
        @endforeach
    </div>

    <!-- Bottom Bar -->
    <div class="exam-bottombar">
        <div>
            <div class="progress-text"><span id="answeredCount">0</span> / {{ $questions->count() }} dijawab</div>
            <div class="question-nav" id="questionNav">
                @foreach($questions as $i => $q)
                <div class="nav-dot {{ isset($existingAnswers[$q->id]) ? 'answered' : '' }}"
                     id="nav-{{ $q->id }}"
                     onclick="document.getElementById('question-{{ $q->id }}').scrollIntoView({behavior:'smooth',block:'center'})">
                    {{ $i + 1 }}
                </div>
                @endforeach
            </div>
        </div>
        <form method="POST" action="/exams/{{ $exam->id }}/submit" id="submitForm">
            @csrf
            <button type="button" class="btn btn-submit" id="submitBtn">
                <i class="fas fa-paper-plane"></i> Kumpulkan
            </button>
        </form>
    </div>

    <script>
    const EXAM_ID = {{ $exam->id }};
    const REMAINING_SECONDS = {{ (int) $remainingSeconds }};
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
    const ACCESS_TOKEN = '{{ $accessToken }}';
    const TOTAL_QUESTIONS = {{ $questions->count() }};
    let tabSwitchCount = 0;
    let timerSeconds = REMAINING_SECONDS;
    let isDialogOpen = false; // Flag to prevent anti-cheat during SweetAlert dialogs
    let isSubmitting = false; // Flag to disable beforeunload on submit

    // ===== TIMER =====
    function updateTimer() {
        const minutes = Math.floor(timerSeconds / 60);
        const seconds = timerSeconds % 60;
        const display = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        document.getElementById('timerDisplay').textContent = display;

        const box = document.getElementById('timerBox');
        box.className = 'timer-box ' + (timerSeconds > 300 ? 'safe' : (timerSeconds > 60 ? 'warning' : 'danger'));

        if (timerSeconds <= 0) {
            clearInterval(timerInterval);
            isSubmitting = true;
            Swal.fire({
                icon: 'warning',
                title: '⏰ Waktu Habis!',
                text: 'Ujian akan dikumpulkan secara otomatis.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                customClass: { popup: 'swal-exam' },
            }).then(() => {
                document.getElementById('submitForm').submit();
            });
            return;
        }
        timerSeconds--;
    }

    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    // ===== ANSWER SAVING =====
    function selectOption(questionId, key, element) {
        const siblings = element.parentElement.querySelectorAll('.mc-option');
        siblings.forEach(s => s.classList.remove('selected'));
        element.classList.add('selected');
        saveAnswer(questionId, key);
    }

    function saveEssay(questionId, value) {
        if (value.trim()) saveAnswer(questionId, value);
    }

    function markUnsaved(questionId) {
        const indicator = document.getElementById('save-' + questionId);
        indicator.textContent = '⏳ Belum tersimpan...';
        indicator.className = 'save-indicator saving';
    }

    async function saveAnswer(questionId, answerText) {
        const indicator = document.getElementById('save-' + questionId);
        indicator.textContent = '💾 Menyimpan...';
        indicator.className = 'save-indicator saving';

        try {
            const res = await fetch('/exams/' + EXAM_ID + '/save-answer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Exam-Token': ACCESS_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    question_id: questionId,
                    answer_text: answerText,
                }),
            });

            const data = await res.json();

            if (data.timeout) {
                isSubmitting = true;
                Swal.fire({
                    icon: 'error',
                    title: '⏰ Waktu Habis!',
                    text: 'Sesi ujian telah berakhir.',
                    allowOutsideClick: false,
                    customClass: { popup: 'swal-exam' },
                }).then(() => {
                    window.location.href = '/exams';
                });
                return;
            }

            if (data.success) {
                indicator.textContent = '✅ Tersimpan';
                indicator.className = 'save-indicator saved';
                document.getElementById('nav-' + questionId)?.classList.add('answered');
                updateAnsweredCount();
            } else {
                indicator.textContent = '❌ Gagal menyimpan';
                indicator.className = 'save-indicator';
            }
        } catch (e) {
            indicator.textContent = '❌ Error jaringan';
            indicator.className = 'save-indicator';
        }
    }

    function updateAnsweredCount() {
        const count = document.querySelectorAll('.nav-dot.answered').length;
        document.getElementById('answeredCount').textContent = count;
    }

    updateAnsweredCount();

    // ===== SUBMIT WITH SWEETALERT2 =====
    document.getElementById('submitBtn').addEventListener('click', function() {
        const answered = document.querySelectorAll('.nav-dot.answered').length;
        const unanswered = TOTAL_QUESTIONS - answered;

        isDialogOpen = true;

        Swal.fire({
            title: 'Kumpulkan Ujian?',
            html: unanswered > 0
                ? `<p style="color:#64748B;font-size:14px;">Anda masih memiliki <strong style="color:#DC2626;">${unanswered} soal</strong> yang belum dijawab.</p><p style="color:#64748B;font-size:13px;margin-top:8px;">Jawaban yang sudah tersimpan tidak bisa diubah setelah dikumpulkan.</p>`
                : `<p style="color:#64748B;font-size:14px;">Semua <strong style="color:#15803D;">${answered} soal</strong> sudah dijawab.</p><p style="color:#64748B;font-size:13px;margin-top:8px;">Jawaban tidak bisa diubah setelah dikumpulkan.</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0D9488',
            cancelButtonColor: '#94A3B8',
            confirmButtonText: '<i class="fas fa-paper-plane"></i> Ya, Kumpulkan!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: { popup: 'swal-exam' },
        }).then((result) => {
            isDialogOpen = false;
            if (result.isConfirmed) {
                isSubmitting = true;
                Swal.fire({
                    title: 'Mengumpulkan...',
                    text: 'Mohon tunggu, ujian sedang dikumpulkan.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading(),
                    customClass: { popup: 'swal-exam' },
                });
                document.getElementById('submitForm').submit();
            }
        });
    });

    // ===== ANTI-CHEAT =====
    // Detect tab switch
    document.addEventListener('visibilitychange', function() {
        if (document.hidden && !isDialogOpen && !isSubmitting) {
            tabSwitchCount++;
            showCheatWarning();
        }
    });

    // Detect window blur (alt-tab etc)
    window.addEventListener('blur', function() {
        if (!isDialogOpen && !isSubmitting) {
            tabSwitchCount++;
            showCheatWarning();
        }
    });

    function showCheatWarning() {
        isDialogOpen = true;
        Swal.fire({
            icon: 'error',
            title: '⚠️ Peringatan Keamanan!',
            html: `<div style="text-align:center;">
                <div style="font-size:48px;font-weight:800;color:#DC2626;margin-bottom:8px;">${tabSwitchCount}</div>
                <p style="color:#64748B;font-size:14px;">Anda terdeteksi meninggalkan halaman ujian.</p>
                <p style="color:#94A3B8;font-size:12px;margin-top:6px;">Pelanggaran ini tercatat oleh sistem.<br>Tindakan berulang dapat mengakibatkan ujian dibatalkan.</p>
            </div>`,
            confirmButtonColor: '#0D9488',
            confirmButtonText: 'Kembali ke Ujian',
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: { popup: 'swal-exam' },
        }).then(() => {
            isDialogOpen = false;
        });
    }

    // Disable right-click
    document.addEventListener('contextmenu', e => e.preventDefault());

    // Disable copy/paste/cut
    document.addEventListener('copy', e => e.preventDefault());
    document.addEventListener('cut', e => e.preventDefault());

    // Disable keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Allow typing in essay textareas
        if (e.target.tagName === 'TEXTAREA') return;

        // Block F12, Ctrl+Shift+I, Ctrl+U, Ctrl+S, Ctrl+C, Ctrl+V, Ctrl+A, PrintScreen
        if (
            e.key === 'F12' ||
            (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i')) ||
            (e.ctrlKey && (e.key === 'u' || e.key === 'U')) ||
            (e.ctrlKey && (e.key === 's' || e.key === 'S')) ||
            (e.ctrlKey && (e.key === 'c' || e.key === 'C')) ||
            (e.ctrlKey && (e.key === 'a' || e.key === 'A')) ||
            e.key === 'PrintScreen'
        ) {
            e.preventDefault();
            return false;
        }
    });

    // Warn before leaving
    window.addEventListener('beforeunload', function(e) {
        if (isSubmitting) return;
        e.preventDefault();
        e.returnValue = 'Ujian sedang berlangsung. Yakin ingin meninggalkan halaman?';
    });
    </script>
</body>
</html>
