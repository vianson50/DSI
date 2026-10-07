/**
 * Scripts du site DSI
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ----- Navigation : fond au défilement ----- */
    const navbar = document.getElementById('navbar');
    const majNavbar = () => {
        navbar.classList.toggle('scrolle', window.scrollY > 40);
    };
    majNavbar();
    window.addEventListener('scroll', majNavbar, { passive: true });

    /* ----- Menu mobile ----- */
    const btnMenu = document.getElementById('btn-menu');
    const menuMobile = document.getElementById('menu-mobile');
    if (btnMenu && menuMobile) {
        btnMenu.addEventListener('click', () => {
            const ouvert = menuMobile.classList.toggle('hidden') === false;
            btnMenu.setAttribute('aria-expanded', String(ouvert));
        });
    }

    /* ----- Apparition au défilement ----- */
    const observateur = new IntersectionObserver((entrees) => {
        entrees.forEach((entree) => {
            if (entree.isIntersecting) {
                entree.target.classList.add('visible');
                observateur.unobserve(entree.target);
            }
        });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach((el) => observateur.observe(el));

    /* ----- Compteurs animés ----- */
    const animCompteur = (el) => {
        const cible = parseInt(el.dataset.cible, 10) || 0;
        const duree = 1800;
        const depart = performance.now();
        const pas = (t) => {
            const progres = Math.min((t - depart) / duree, 1);
            el.textContent = Math.round(cible * (1 - Math.pow(1 - progres, 3)));
            if (progres < 1) requestAnimationFrame(pas);
        };
        requestAnimationFrame(pas);
    };
    const obsCompteurs = new IntersectionObserver((entrees) => {
        entrees.forEach((entree) => {
            if (entree.isIntersecting) {
                animCompteur(entree.target);
                obsCompteurs.unobserve(entree.target);
            }
        });
    }, { threshold: 0.6 });
    document.querySelectorAll('.compteur').forEach((el) => obsCompteurs.observe(el));

    /* ----- Formulaire de contact ----- */
    const formulaire = document.getElementById('formulaire-contact');
    if (formulaire) {
        // Compteur de caractères
        const zoneMessage = document.getElementById('message');
        const compteur = document.getElementById('compteur-caracteres');
        if (zoneMessage && compteur) {
            const maj = () => { compteur.textContent = zoneMessage.value.length; };
            zoneMessage.addEventListener('input', maj);
            maj();
        }

        // Validation visuelle en direct
        formulaire.querySelectorAll('input[required], textarea[required]').forEach((champ) => {
            champ.addEventListener('blur', () => {
                const valide = champ.checkValidity();
                champ.classList.toggle('border-red-400', !valide);
                champ.classList.toggle('border-emerald-400', valide && champ.value.trim() !== '');
            });
            champ.addEventListener('input', () => {
                if (champ.checkValidity()) {
                    champ.classList.remove('border-red-400');
                }
            });
        });

        // État du bouton pendant l'envoi
        formulaire.addEventListener('submit', () => {
            const honeypot = formulaire.querySelector('[name="site_web"]');
            if (honeypot && honeypot.value !== '') return; // bot détecté

            const btn = document.getElementById('btn-envoyer');
            const texte = document.getElementById('texte-btn');
            if (btn) btn.disabled = true;
            if (texte) texte.textContent = 'Envoi en cours…';
        });
    }

    /* ----- Défilement doux vers l'alerte de succès ----- */
    const alerte = document.getElementById('alerte-succes');
    if (alerte) {
        alerte.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    /* ----- Bandeau de consentement cookies ----- */
    const bandeau = document.getElementById('bandeau-cookies');
    const modal = document.getElementById('modal-cookies');
    const CLE_CONSENTEMENT = 'consentement_cookies';
    const DUREE_MS = 182 * 24 * 60 * 60 * 1000; // 6 mois

    const lireConsentement = () => {
        try {
            const brut = localStorage.getItem(CLE_CONSENTEMENT);
            if (!brut) return null;
            const donnees = JSON.parse(brut);
            return Date.now() < donnees.expiration ? donnees.choix : null;
        } catch {
            return null;
        }
    };

    const enregistrerConsentement = (choix) => {
        localStorage.setItem(CLE_CONSENTEMENT, JSON.stringify({
            choix,
            date: new Date().toISOString(),
            expiration: Date.now() + DUREE_MS,
        }));
        // Cookie PHP correspondant (accessible côté serveur si besoin)
        document.cookie = `consentement_cookies=${choix};path=/;max-age=${DUREE_MS / 1000};SameSite=Lax`;
    };

    if (bandeau) {
        if (lireConsentement() === null) {
            bandeau.classList.remove('hidden');
        }

        const accepter = document.getElementById('btn-accepter-cookies');
        const refuser = document.getElementById('btn-refuser-cookies');
        const enSavoirPlus = document.getElementById('btn-en-savoir-plus');
        const fermerModal = document.getElementById('btn-fermer-modal');
        const fondModal = document.getElementById('fond-modal-cookies');

        if (accepter) accepter.addEventListener('click', () => {
            enregistrerConsentement('accepte');
            bandeau.classList.add('hidden');
        });
        if (refuser) refuser.addEventListener('click', () => {
            enregistrerConsentement('refuse');
            bandeau.classList.add('hidden');
        });
        if (enSavoirPlus && modal) enSavoirPlus.addEventListener('click', () => modal.classList.remove('hidden'));
        if (fermerModal && modal) fermerModal.addEventListener('click', () => modal.classList.add('hidden'));
        if (fondModal && modal) fondModal.addEventListener('click', () => modal.classList.add('hidden'));
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal) modal.classList.add('hidden');
        });
    }
});
