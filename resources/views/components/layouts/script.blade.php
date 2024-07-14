<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" async></script>
<script>
    // Mendengarkan event submit pada container gacha
    document.getElementById('gachaContainer').addEventListener('submit', function(event) {
        event.preventDefault();

        // Identifikasi form yang dipilih
        const formId = event.target.id;

        // Menentukan endpoint berdasarkan form
        const endpoint = (formId === 'gachaForm') ? '/perform-gacha' : '/perform-ten-gacha';

        // Melakukan request ke endpoint yang sesuai
        axios.post(endpoint)
            .then(function(response) {
                const resultDiv = document.getElementById('gachaResult');
                const pullStatus = document.getElementById('pullStatus');
                resultDiv.innerHTML = '';
                pullStatus.innerHTML = '';

                if (response.data.success) {
                    if (formId === 'gachaForm') {
                        const weapon = response.data.data;
                        appendWeaponResult(resultDiv, weapon);
                        updatePullStatus(pullStatus, weapon, formId);
                    } else if (formId === 'gacha-ten-pull') {
                        const weapons = response.data.data;
                        let totalPulls = response.data.totalPulls;
                        let pitty4 = response.data.pitty4;
                        let pitty5 = response.data.pitty5;

                        weapons.forEach(weapon => {
                            appendWeaponResult(resultDiv, weapon);
                        });
                        
                        updatePullStatus(pullStatus, {
                            totalPulls,
                            pitty4,
                            pitty5
                        }, formId);
                    }
                } else {
                    displayErrorMessage(resultDiv, response.data.message);
                }
            })
            .catch(function(error) {
                console.error('An error occurred:', error);
            });
    });

    // Fungsi untuk menambahkan hasil gacha ke DOM
    function appendWeaponResult(resultDiv, weapon) {
        
       const cardHTML = `
    <div class="relative card bg-gray-800 rounded-lg overflow-hidden shadow-lg p-2">
        <div class="absolute top-0 left-0 bg-yellow-500 text-white text-xs font-bold p-1">New</div>
        <img class="w-full h-32 object-cover" src="${weapon.img}" alt="${weapon.name}" style="background-color: ${getBackgroundColor(weapon.rarity)}"/>
        <div class="p-1">
            <div class="flex justify-center">
                <span class="text-yellow-400">
                    ${
                        weapon.rarity === 1 ? '★★★★★' :
                        weapon.rarity === 2 ? '★★★★' :
                        weapon.rarity === 3 ? '★★★' :
                        ''
                    }
                </span>
            </div>
        </div>
    </div>
`;
        resultDiv.insertAdjacentHTML('beforeend', cardHTML);
    }

    // Fungsi untuk memperbarui status pull
    function updatePullStatus(pullStatus, data, formId) {
        let statusHTML;

        if (formId === 'gachaForm') {
            statusHTML = `
                <ul class="list-disc ml-4">
                    <li>Total Summons: ${data.totalPulls}x</li>
                    <li>Summons since last 4★ or higher: ${data.pitty4}</li>
                    <li>Summons since last 5★: ${data.pitty5}</li>
                </ul>
            `;
        } else if (formId === 'gacha-ten-pull') {
            statusHTML = `
                <ul class="list-disc ml-4">
                    <li>Total Summons: ${data.totalPulls}x</li>
                    <li>Summons since last 4★ or higher: ${data.pitty4}</li>
                    <li>Summons since last 5★: ${data.pitty5}</li>
                </ul>
            `;
        }

        pullStatus.innerHTML = statusHTML;
    }

    // Fungsi untuk menentukan warna latar belakang berdasarkan rarity
    function getBackgroundColor(rarity) {
        switch (rarity) {
            case 1:
                return '#ffe0a9';
            case 2:
                return '#df96e6';
            case 3:
                return 'cyan';
            default:
                return 'white';
        }
    }

    // Fungsi untuk menampilkan pesan kesalahan
    function displayErrorMessage(resultDiv, message) {
        resultDiv.innerHTML = `
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">${message}</span>
            </div>
        `;
    }
</script>
