export class InfiniteScrollElement extends HTMLElement {
	/** @type {IntersectionObserver|undefined} */
	#observer;
	#running = false;
	#clear = null;

	connectedCallback() {
		this.innerHTML = '';

		const offset = this.dataset.infiniteScrollOffset;

		this.#observer = new IntersectionObserver(this.#onIntersection.bind(this), {
			rootMargin: offset || '300px',
		});
		this.#observer.observe(this);
	}

	disconnectedCallback() {
		this.#clear?.();
		this.#observer?.disconnect();
	}

	#onIntersection(entries) {
		const entry = entries[0];

		if (!entry || !entry.isIntersecting) {
			return;
		}

		if (this.#running) {
			return;
		}

		this.#running = true;

		// loading element
		const loading = document.createElement('div');
		loading.classList.add('infinite-scroll-loading');

		// timeout prevent
		let timeout = setTimeout(() => {
			if (this.isConnected) {
				this.appendChild(loading);
			}
		}, 100);

		// clear function
		this.#clear = () => {
			if (timeout) {
				clearTimeout(timeout);

				timeout = undefined;
			} else {
				loading.remove();
			}

			this.#clear = undefined;
		};

		// make request
		const link = this.dataset.infiniteScrollLink;

		if (!link || !link.length) {
			console.error('InfiniteScrollElement: Missing link attribute for', this);

			return;
		}

		const promise = this.#makeRequest(link);

		// post process
		let processed = false;

		if (promise && typeof promise === 'object' && 'then' in promise) {
			processed = true;
			promise.then(() => this.#clear?.());
		}

		if (promise && typeof promise === 'object' && 'then' in promise) {
			processed = true;
			promise.catch(() => this.#clear?.());
		}

		if (!processed) {
			this.#clear?.();
		}
	}

	/**
	 * @param {string} link
	 * @return {Promise<any>}
	 */
	#makeRequest(link) {
		// TODO make nette ajax request and return promise
	}

}
