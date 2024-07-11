<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.getElementById('gachaForm').addEventListener('submit', function(e) {
        e.preventDefault();

        axios.post('/perform-gacha')
            .then(function(response) {
                const resultDiv = document.getElementById('gachaResult');
                const pullStatus = document.getElementById('pullStatus');

                resultDiv.innerHTML = '';
                pullStatus.innerHTML = '';

                if (response.data.success) {
                    const weapon = response.data.data;

                    // Create and append result card
                    const cardHTML = `
                <div class="custom-result-card">
                    <h3>${weapon.name}</h3>
                    <ul>
                        <li>ID: ${weapon.id}</li>
                        <li>Type: ${weapon.type}</li>
                        <li>Rarity: ${weapon.rarity}</li>
                    </ul>
                </div>
            `;
                    resultDiv.innerHTML += cardHTML;

                    // Update pull status
                    const statusHTML = `
                <div class="custom-pull-status">
                    <p>Total Summons: ${weapon.totalPulls}x</p>
                    <p>Summons since last 4★ or higher: ${weapon.pitty4}</p>
                    <p>Summons since last 5★: ${weapon.pitty5}</p>
                </div>
            `;
                    pullStatus.innerHTML = statusHTML;
                } else {
                    resultDiv.innerHTML = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">${response.data.message}</span>
                </div>
            `;
                }
            })
            .catch(function(error) {
                console.error('An error occurred:', error);
            });

    });

    document.getElementById('gacha-ten-pull').addEventListener('submit', function(e) {
        e.preventDefault();

        axios.post('/perform-ten-gacha')
            .then(function(response) {
                const resultDiv = document.getElementById('gachaResult');
                const pullStatus = document.getElementById('pullStatus');

                resultDiv.innerHTML = '';
                pullStatus.innerHTML = '';

                if (response.data.success) {
                    const weapons = response.data.data;

                    // Build HTML for each weapon
                    let resultHTML = '<div class="flex flex-wrap">';
                    weapons.forEach(weapon => {
                        resultHTML += `
                        <div class="custom-result-card mx-4 my-2">
                            <h3>${weapon.name}</h3>
                            <ul>
                                <li>ID: ${weapon.id}</li>
                                <li>Type: ${weapon.type}</li>
                                <li>Rarity: ${weapon.rarity}</li>
                            </ul>
                        </div>
                    `;
                    });
                    resultHTML += '</div>';
                    resultDiv.innerHTML = resultHTML;

                    // Update pull status
                    const statusHTML = `
                    <ul class="list-disc ml-4">
                        <li>Total Summons: ${response.data.totalPulls}x</li>
                        <li>Summons since last 4★ or higher: ${response.data.pitty4}</li>
                        <li>Summons since last 5★: ${response.data.pitty5}</li>
                    </ul>
                `;
                    pullStatus.innerHTML = statusHTML;
                } else {
                    resultDiv.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">${response.data.message}</span>
                    </div>
                `;
                }
            })
            .catch(function(error) {
                console.error('An error occurred:', error);
            });
    });
</script>
