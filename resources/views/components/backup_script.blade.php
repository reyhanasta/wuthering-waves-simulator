<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" async></script>
<script>
    // Mendengarkan event submit pada container gacha
    document.getElementById('gachaContainer').addEventListener('submit', async function(event) {
        event.preventDefault();

        // Identifikasi form yang dipilih
        const formId = event.target.id;
        // Menentukan endpoint berdasarkan form
        const endpoint = (formId === 'gachaForm') ? '/perform-gacha' : '/perform-ten-gacha';
        // Menampilkan loading text
        const loadingText = document.getElementById('loadingText');
        // Sembunyikan gacha results
        const gachaResult = document.getElementById('gachaResult');
        const pullStatus = document.getElementById('pullStatus');

        loadingText.style.display = 'flex';
        gachaResult.style.display = 'none';

        // Melakukan request ke endpoint yang sesuai
        try {
            const response = await axios.post(endpoint);
            gachaResult.innerHTML = '';
            pullStatus.innerHTML = '';

            if (response.data.success) {
                const {
                    data,
                    totalPulls,
                    pitty4,
                    pitty5
                } = response.data;
                if (formId === 'gachaForm') {
                    appendWeaponResult(gachaResult, data);
                    updatePullStatus(pullStatus, {
                        totalPulls,
                        pitty4,
                        pitty5
                    });
                } else if (formId === 'gacha-ten-pull') {
                    data.forEach(weapon => appendWeaponResult(gachaResult, weapon));
                    updatePullStatus(pullStatus, {
                        totalPulls,
                        pitty4,
                        pitty5
                    });
                }
            } else {
                displayErrorMessage(gachaResult, response.data.message);
            }
        } catch (error) {
            console.error('An error occurred:', error);
            displayErrorMessage(gachaResult, 'Gacha failed. Please try again.');
        } finally {
            loadingText.style.display = 'none';
            gachaResult.style.display = 'grid';
            gachaResult.classList.remove('grid-cols-1', 'grid-cols-5'); // Hapus kelas sebelumnya
            if (formId === 'gachaForm') {
                gachaResult.classList.add('grid-cols-1'); // Set 1 kolom
            } else {
                gachaResult.classList.add('grid-cols-5'); // Set 5 kolom
            }
        }
    });

    // Fungsi untuk menambahkan hasil gacha ke DOM
    function appendWeaponResult(gachaResult, weapon) {

        const cardHTML = `
    <div class="relative bg-gray-800 rounded-lg overflow-hidden shadow-lg p-2">$
        <div class="absolute top-0 left-0 bg-yellow-500 text-white text-xs font-bold p-1">New</div>
        <img class="w-full h-32 object-cover" src="${weapon.img}" alt="${weapon.name}" style="background-color: ${getBackgroundColor(weapon.rarity)}"/>
        <div class="p-2">
            <div class="flex justify-center">
                <span class="text-yellow-400 text-xs">
                    ${
                        weapon.rarity === 1 ? '★★★★★' :
                        weapon.rarity === 2 ? '★★★★' :
                        weapon.rarity === 3 ? '★★★' :
                        ''
                    }
                </span>
            </div>
            <div class="text-center mt-2">
                <p class="text-white">${weapon.name}</p>
            </div>
        </div>
    </div>
`;
        gachaResult.insertAdjacentHTML('beforeend', cardHTML);


    }

    // Fungsi untuk memperbarui status pull
    function updatePullStatus(pullStatus, {
        totalPulls,
        pitty4,
        pitty5
    }) {
        const statusHTML = `
            <ul class="list-disc ml-4">
                <li>Total Summons: ${totalPulls}x</li>
                <li>Summons since last 4★ or higher: ${pitty4}</li>
                <li>Summons since last 5★: ${pitty5}</li>
            </ul>
        `;
        pullStatus.innerHTML = statusHTML;
    }

    // Fungsi untuk menentukan warna latar belakang berdasarkan rarity
    function getBackgroundColor(rarity) {
        const colors = {
            1: '#ffe0a9',
            2: '#df96e6',
            3: 'cyan'
        };
        return colors[rarity] || 'white';
    }

    // Fungsi untuk menampilkan pesan kesalahan
    function displayErrorMessage(gachaResult, message) {
        gachaResult.innerHTML = `
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">${message}</span>
            </div>
        `;
    }
</script>
