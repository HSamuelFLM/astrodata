/**
 * AstroData - JavaScript
 * Validação de formulário e pequenas interações
 */

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-signo');
    const inputData = document.getElementById('data_nascimento');

    if (form && inputData) {
        form.addEventListener('submit', function (e) {
            // Remove feedback anterior
            inputData.classList.remove('is-invalid');

            const valor = inputData.value.trim();

            if (!valor) {
                e.preventDefault();
                inputData.classList.add('is-invalid');
                inputData.focus();
                return;
            }

            // Validação de data futura (redundante com o atributo max, mas reforça)
            const dataEscolhida = new Date(valor + 'T00:00:00');
            const hoje = new Date();
            hoje.setHours(0, 0, 0, 0);

            if (dataEscolhida > hoje) {
                e.preventDefault();
                inputData.classList.add('is-invalid');
                inputData.focus();
                return;
            }

            // Feedback visual de carregamento no botão
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = 'Consultando os astros...';
            }
        });

        // Remove classe de erro ao digitar/selecionar
        inputData.addEventListener('change', function () {
            this.classList.remove('is-invalid');
        });
    }

    // Pequena animação de entrada já tratada via CSS (fade-in)
});
