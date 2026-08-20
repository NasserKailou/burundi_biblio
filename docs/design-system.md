# Design system — Bibliotheque Numerique Scolaire (BNS)

Produit avec le skill `ui-ux-pro-max` (recherches ciblees sur les domaines `product`,
`color`, `typography`, `ux`, `chart` et le stack `laravel` — voir methode ci-dessous).
Reference d'inspiration UX : mesmanuels.fr et lelivrescolaire.fr (grille de couvertures,
lecteur immersif, navigation par niveau/matiere) — references de structure uniquement,
aucun contenu ni asset reutilise.

## Methode

Les deux premieres requetes `--design-system` (mode auto-agrege du skill) ont retourne
des archetypes hors-sujet pour notre produit : une page d'atterrissage marketing
"Feature-Rich Showcase" en Claymorphism (trop enfantin, pense pour une app kids/SaaS)
puis un dashboard "Real-Time Operations" en Glassmorphism sombre (trop "NOC/DevOps").
Aucun des deux ne correspond a un outil intranet scolaire utilise pour lire des manuels
et gerer un catalogue. Conformement au protocole de repli du skill ("retry once, sinon
combiner des recherches ciblees"), le design ci-dessous synthetise des requetes
`--domain` individuelles (product/color/typography/ux/chart/stack) plutot que le mode
auto-agrege. La palette "LMS (Learning Management System)" et la paire typographique
"Corporate Trust" (Lexend + Source Sans 3, orientee accessibilite/gouvernement/sante)
sont les matchs les plus pertinents trouves ; ils sont repris ci-dessous avec des
ajustements de contraste documentes (voir "Corrections apportees").

## Couleurs

Definies comme variables CSS dans `resources/css/app.css` (`:root`) et mappees dans
`tailwind.config.js` sous `theme.extend.colors.bns.*`. Usage : `bg-bns-primary`,
`text-bns-foreground`, etc. Palette unique pour toute l'app (eleve/enseignant/admin) —
la coherence prime sur la differenciation visuelle par role, qui se fait par layout
(sidebar admin) et non par une palette differente.

| Rôle | Hex | Variable CSS | Classe Tailwind |
|---|---|---|---|
| Primary (teal education) | `#0F766E` | `--color-primary` | `bg-bns-primary` |
| On Primary | `#FFFFFF` | `--color-on-primary` | `text-bns-on-primary` |
| Accent (ambre — badges, favoris, "commun") | `#D97706` | `--color-accent` | `bg-bns-accent` |
| On Accent | `#78350F` | `--color-on-accent` | `text-bns-on-accent` |
| Background (app) | `#F8FAFC` | `--color-background` | `bg-bns-background` |
| Foreground (texte principal) | `#0F172A` | `--color-foreground` | `text-bns-foreground` |
| Card | `#FFFFFF` | `--color-card` | `bg-bns-card` |
| Muted (fonds secondaires) | `#F1F5F9` | `--color-muted` | `bg-bns-muted` |
| Muted foreground | `#475569` | `--color-muted-foreground` | `text-bns-muted-foreground` |
| Border | `#E2E8F0` | `--color-border` | `border-bns-border` |
| Success | `#059669` | `--color-success` | `text-bns-success` |
| Destructive | `#DC2626` | `--color-destructive` | `text-bns-destructive` |
| Ring (focus clavier) | `#0F766E` | `--color-ring` | `ring-bns-ring` |
| Reader background (lecteur immersif) | `#FAF9F6` | `--color-reader-bg` | `bg-bns-reader` |
| Reader toolbar | `#1E293B` | `--color-reader-toolbar` | `bg-bns-reader-toolbar` |

### Corrections apportees au match brut du skill

- La recherche `--domain color` proposait `On Primary: #000000` sur un primary
  `#0D9488` : ratio de contraste insuffisant (~2.3:1). Corrige en assombrissant le
  primary a `#0F766E` (teal-700) avec `On Primary: #FFFFFF` (ratio ~4.6:1, conforme
  AA texte normal).
- L'accent ambre `#D97706` n'est volontairement PAS utilise comme fond de bouton
  pleine taille avec texte blanc (contraste ~2.9:1, insuffisant) : reserve aux
  badges/etoiles favoris/puces "commun" ou seul un texte fonce (`#78350F`) ou une
  icone sont poses dessus.
- `Reader toolbar` en slate-800 fonce est un choix deliberement distinct du theme
  clair de l'app : c'est la convention etablie des lecteurs de documents (PDF.js,
  Google Docs viewer) — reconnaissable instantanement, pas assimilable a un
  "dark mode" de l'application.

## Typographie

Paire "Corporate Trust" (skill, domaine `typography`) : **Lexend** (titres, boutons,
nav — concue pour ameliorer la vitesse de lecture, particulierement pertinente pour un
public d'eleves) + **Source Sans 3** (corps de texte, tableaux, formulaires).

```css
@import url('https://fonts.googleapis.com/css2?family=Lexend:wght@500;600;700&family=Source+Sans+3:wght@400;500;600&display=swap');
```

```js
// tailwind.config.js
fontFamily: {
  heading: ['Lexend', ...defaultTheme.fontFamily.sans],
  sans: ['"Source Sans 3"', ...defaultTheme.fontFamily.sans],
}
```

- Taille de base : 16px, `line-height: 1.5` (corps de texte).
- Titres : `font-heading font-semibold`.
- Ne jamais descendre sous 12px pour du texte de contenu (checklist skill).

## Composants Blade (`resources/views/components/`)

Convention retenue via le skill (stack `laravel`) : composants Blade anonymes
(`x-*`), pas de `@include` pour de l'UI reutilisable. Props via `@props`.

| Composant | Usage |
|---|---|
| `<x-button>` | Bouton principal (`variant="primary\|secondary\|danger\|ghost"`) |
| `<x-input>` | Champ de formulaire avec label, erreur, aide integres |
| `<x-select>` | Liste deroulante avec label/erreur |
| `<x-card>` | Conteneur carte (fond blanc, bordure, ombre legere) |
| `<x-alert>` | Bandeau de statut (`type="success\|error\|warning\|info"`) |
| `<x-badge>` | Etiquette courte (statut manuel, "commun", niveau) |
| `<x-book-cover>` | Vignette de couverture manuel (grille catalogue) |
| `<x-stat-card>` | Carte chiffre-cle (dashboards) |
| `<x-sidebar-link>` | Lien de navigation laterale (back-office admin) |

## UX — regles retenues (skill, domaine `ux`)

- **Navigation clavier complete** : chaque controle interactif doit rester
  atteignable au clavier avec un focus visible (`ring-2 ring-bns-ring`) — severite
  haute, verifie sur les ecrans catalogue/lecteur/back-office.
- **Nav laterale fixe (admin)** : compenser par `padding-left` sur le contenu, ne
  jamais superposer la sidebar au contenu.
- **Fil d'Ariane** : utilise dans le back-office admin (3+ niveaux de profondeur :
  Admin > Utilisateurs > Fiche utilisateur) ; pas sur les ecrans a plat (catalogue).
- **Formulaires** : label toujours visible (jamais placeholder-only), erreur affichee
  au plus pres du champ concerne (implémenté dans `<x-input>`).

## Graphiques (Chart.js — module statistiques, étape 9)

Regle retenue du skill (domaine `chart`) : **la couleur ne doit jamais etre le seul
vecteur d'information**. Chaque serie/segment a un libelle texte visible (legende +
tooltip), et un tableau de donnees accessible est fourni en alternative pour les
graphiques complexes. Palette categorique pour les graphiques (matieres, niveaux) :
derivee de la palette principale + variantes, exposee en JS dans
`resources/js/bns-colors.js` pour etre consommee directement par Chart.js (les classes
Tailwind ne sont pas utilisables dans la config JS de Chart.js).

## Icônes

SVG uniquement (Heroicons), jamais d'emoji comme icone fonctionnelle (checklist skill).

## Responsive

Points de rupture verifies : 375px (non prioritaire — app pensee desktop/tablette
d'etablissement, mais ne doit pas casser), 768px (tablette — cible principale avec le
desktop), 1024px, 1440px. Pas de scroll horizontal en dehors des conteneurs de tableaux
(`overflow-x-auto`).
