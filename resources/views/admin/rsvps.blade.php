
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar RSVP — Wedding Admin</title>
    <link rel="stylesheet" href="{{ asset('css/invitation.css') }}">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f7f4ed;
            color: #30251f;
            font-family: Arial, sans-serif;
        }

        .dashboard {
            max-width: 1200px;
            margin: auto;
            padding: 28px 20px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .dashboard-header h1 {
            margin: 0;
            color: #6e2638;
            font-family: "Cormorant Garamond", serif;
            font-size: 36px;
        }

        .dashboard-header p {
            color: #78665c;
        }

        .dashboard-header form {
            margin: 0;
        }

        .admin-button {
            padding: 11px 16px;
            border: 1px solid #6e2638;
            background: #6e2638;
            color: #fffdf8;
            cursor: pointer;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 26px;
        }

        .stat-card {
            padding: 20px;
            background: #fffdf8;
            border: 1px solid #e0d3b8;
        }

        .stat-card p {
            margin: 0 0 12px;
            color: #78665c;
            font-size: 13px;
        }

        .stat-card strong {
            color: #6e2638;
            font-size: 28px;
        }

        .table-wrapper {
            overflow-x: auto;
            background: #fffdf8;
            border: 1px solid #e0d3b8;
        }

        .table-wrapper h2 {
            padding: 0 18px;
            color: #6e2638;
            font-family: "Cormorant Garamond", serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid #eee5d9;
            text-align: left;
            vertical-align: top;
            font-size: 14px;
        }

        th {
            background: #f4ecdf;
            color: #6e2638;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 12px;
            white-space: nowrap;
        }

        .status--yes {
            background: #e7efdf;
            color: #31552d;
        }

        .status--no {
            background: #f7e5e2;
            color: #852c35;
        }

        .empty-state {
            padding: 30px;
            color: #78665c;
            text-align: center;
        }

        .pagination {
            padding: 18px;
        }

        @media (max-width: 800px) {
            .stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-header h1 {
                font-size: 30px;
            }
        }

        @media (max-width: 400px) {
            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main class="dashboard">
        <header class="dashboard-header">
            <div>
                <h1>Wedding RSVP</h1>
                <p>Daftar konfirmasi kehadiran tamu.</p>
            </div>

            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="admin-button">
                    Keluar
                </button>
            </form>
        </header>

        <section class="stats">
            <article class="stat-card">
                <p>Total Konfirmasi</p>
                <strong>{{ $totalResponses }}</strong>
            </article>

            <article class="stat-card">
                <p>Konfirmasi Hadir</p>
                <strong>{{ $attendingResponses }}</strong>
            </article>

            <article class="stat-card">
                <p>Tidak Bisa Hadir</p>
                <strong>{{ $notAttendingResponses }}</strong>
            </article>

            <article class="stat-card">
                <p>Total Orang yang Hadir</p>
                <strong>{{ $attendingGuests }}</strong>
            </article>
        </section>

        <section class="table-wrapper">
            <h2>Daftar Tamu</h2>

            @if ($rsvps->isEmpty())
                <div class="empty-state">
                    Belum ada konfirmasi kehadiran yang masuk.
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Status</th>
                            <th>Jumlah</th>
                            <th>Ucapan</th>
                            <th>Waktu Konfirmasi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($rsvps as $rsvp)
                            <tr>
                                <td>{{ $rsvp->name }}</td>

                                <td>
                                    @if ($rsvp->attendance === 'hadir')
                                        <span class="status status--yes">
                                            Hadir
                                        </span>
                                    @else
                                        <span class="status status--no">
                                            Tidak hadir
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $rsvp->guests }}
                                    orang
                                </td>

                                <td>
                                    {{ $rsvp->message ?: '—' }}
                                </td>

                                <td>
                                    {{ $rsvp->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination">
                    {{ $rsvps->links() }}
                </div>
            @endif
        </section>
    </main>
</body>
</html>

