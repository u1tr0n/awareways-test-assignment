import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['variant', 'leftColumn', 'rightColumn', 'btn']

    moveLeft(event) {
        const newDiv = document.createElement('div');
        const newSubDiv = document.createElement('div');
        newSubDiv.textContent = event.target.parentElement.querySelectorAll('.answer')[0].innerHTML;
        newSubDiv.classList.add('py-2','answer');
        newDiv.appendChild(newSubDiv);
        newDiv.classList.add('w-100','text-center','mb-2');
        this.leftColumnTarget.appendChild(newDiv);
        event.target.parentElement.remove();
        if (this.variantTarget.children.length < 1) {
            this.btnTarget.disabled = false;
        }

    }
    moveRight(event) {
        const newDiv = document.createElement('div');
        const newSubDiv = document.createElement('div');
        newSubDiv.textContent = event.target.parentElement.querySelectorAll('.answer')[0].innerHTML;
        newSubDiv.classList.add('py-2','answer');

        newDiv.appendChild(newSubDiv);
        newDiv.classList.add('w-100','text-center','mb-2');

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'answers[]';
        hidden.value = event.params.id;

        newDiv.appendChild(hidden);

        this.rightColumnTarget.appendChild(newDiv);
        event.target.parentElement.remove();
        if (this.variantTarget.children.length < 1) {
            this.btnTarget.disabled = false;
        }
    }
}
