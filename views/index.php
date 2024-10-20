<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <link rel="stylesheet" href="/styles/style.min.css">
</head>
<body class="bg-dark-navy text-white">
    <main class="min-h-full-view my-10">
        <h1 class="text-4xl text-center">
            <span class="text-red">
                Event
            </span>
            Management System
        </h1>

        <div class="rounded-sm p-4 bg-navy w-70% mx-auto my-10">
            <h2 class="text-2xl text-white text-start mb-4">
                Events Overview
            </h2>
            <table class="w-full border-collapse rounded-sm">
                <thead> 
                    <tr class="bg-white text-navy">
                        <th class="p-2">Event</th>
                        <th class="p-2">Capacity Rate</th>
                        <th class="p-2">Visitors</th>
                        <th class="p-2">Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($events) && count($events) > 0): ?>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td class="border-b border-t-0 border-s-0 border-e-0 border-white border-solid border-collapse p-2 text-center">
                                    <?= htmlspecialchars($event->name) ?>
                                </td>
                                <td class="border-b border-t-0 border-s-0 border-e-0 border-white border-solid border-collapse p-2 text-center text-lg relative">
                                    <?= htmlspecialchars($event->getCapacityRatePercentage()) ?>
                                    <?php if ($event->getCapacityRate() >= 80): ?>
                                        <svg fill="currentColor" class="text-red w-3 h-3 absolute top-1/2 end-1/4 translate-middle" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"  width="800px" height="800px" viewBox="0 0 478.125 478.125" xml:space="preserve">
                                            <circle cx="239.904" cy="314.721" r="35.878"/>
                                            <path d="M256.657,127.525h-31.9c-10.557,0-19.125,8.645-19.125,19.125v101.975c0,10.48,8.645,19.125,19.125,19.125h31.9 c10.48,0,19.125-8.645,19.125-19.125V146.65C275.782,136.17,267.138,127.525,256.657,127.525z"/>
                                            <path d="M239.062,0C106.947,0,0,106.947,0,239.062s106.947,239.062,239.062,239.062c132.115,0,239.062-106.947,239.062-239.062 S371.178,0,239.062,0z M239.292,409.734c-94.171,0-170.595-76.348-170.595-170.596c0-94.248,76.347-170.595,170.595-170.595 s170.595,76.347,170.595,170.595C409.887,333.387,333.464,409.734,239.292,409.734z"/>
                                        </svg>
                                    <?php endif; ?>
                                </td>
                                <td class="border-b border-t-0 border-s-0 border-e-0 border-white border-solid border-collapse p-2 text-center">
                                    <?= htmlspecialchars($event->getEventParticipantsCount()) ?> / <?= htmlspecialchars($event->getCapacity()) ?>
                                </td>
                                <td class="border-b border-t-0 border-s-0 border-e-0 border-white border-solid border-collapse p-2 text-center">
                                    <?= htmlspecialchars($event->location()->name) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-xl p-4">
                                No Events Found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="rounded-sm p-4 bg-navy w-70% mx-auto my-10">
            <h2 class="text-2xl text-white text-start mb-4">
                API Blacklist
            </h2>
            <table class="w-full border-collapse rounded-sm">
                <thead> 
                    <tr class="bg-white text-navy">
                        <th class="p-2">IP Addres</th>
                        <th class="p-2">Blocked at</th>
                        <th class="p-2">Unblock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($ips) && count($ips) > 0): ?>
                        <?php foreach ($ips as $ip): ?>
                            <tr>
                                <td class="border-b border-t-0 border-s-0 border-e-0 border-white border-solid border-collapse p-2 text-center">
                                    <?= htmlspecialchars($ip->ip_address) ?>
                                </td>
                                <td class="border-b border-t-0 border-s-0 border-e-0 border-white border-solid border-collapse p-2 text-center">
                                <?= htmlspecialchars($ip->created_at->format("d.m.Y H:i:s")) ?>
                                </td>
                                <td class="border-b border-t-0 border-s-0 border-e-0 border-white border-solid border-collapse p-2 text-center">
                                    <form method="POST" action="/blacklisted/unblock/<?= htmlspecialchars($ip->id) ?>">
                                        <input type="hidden" name="id" value="">
                                        <button class="bg-red rounded p-2 border-0 font-bold text-white cursor-pointer transiton hover:bg-dark-red">
                                            Unblock
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-xl p-4">
                                No Blacklisted IPs.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</body>
</html>
