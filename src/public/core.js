function makeid(
  length,
  characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"
) {
  var result = "";
  var charactersLength = characters.length;
  for (var i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength));
  }
  return result;
}

function choice(array) {
  return array[Math.floor(Math.random() * array.length)];
}

function between(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

function randomCity() {
  return choice([
    "Paris",
    "Lyon",
    "Marseille",
    "Toulouse",
    "Nice",
    "Bordeaux",
    "Lille",
    "Strasbourg",
    "Nantes",
    "Rennes",
    "Toulon",
    "Aix-en-Provence",
    "Marignane",
    "Cannes",
  ]);
}

function randomAddress() {
  return (
    // random number between 1 and 100
    Math.floor(Math.random() * 100) +
    " " +
    choice([
      "Rue",
      "Boulevard",
      "Avenue",
      "Place",
      "Impasse",
      "Chemin",
      "Allée",
      "Rond-point",
      "Square",
      "Cours",
    ]) +
    " " +
    // nom de rue entre 2 et 6 lettres
    choice([
      "des Mimosas",
      "des Roses",
      "des Lilas",
      "des Tulipes",
      "des Jasmin",
      "des Orangers",
      "des Pommes",
      "des Poires",
      "des Abricots",
      "des Pétunias",
      "des Lys",
      "des Narcisses",
      "des Iris",
      "des Fleurs de Lys",
    ])
  );
}

function randomPrenom() {
  return choice([
    "Marie",
    "Pierre",
    "Paul",
    "Jacques",
    "Jeanne",
    "Luc",
    "Sophie",
    "Thomas",
    "Emma",
    "Hugo",
    "Chloé",
    "Louis",
    "Camille",
    "Nathan",
    "Léa",
    "Thomas",
    "Emma",
  ]);
}

function randomNom() {
  return choice([
    "Dupont",
    "Martin",
    "Bernard",
    "Thomas",
    "Durand",
    "Moreau",
    "Garcia",
    "Martin",
    "Bernard",
    "Thomas",
    "Durand",
    "Moreau",
    "Garcia",
  ]);
}

function randomEmail() {
  return (
    randomPrenom().toLowerCase() +
    "." +
    randomNom().toLowerCase() +
    "@gmail.com"
  );
}

function randomCodePostal() {
  return makeid(5, "0123456789");
}

function randomProfession() {
  return choice(["employé", "retraité", "étudiant", "autre"]);
}

function randomAge() {
  return Math.floor(Math.random() * 100) + 18;
}

function randomTel() {
  return "+336" + makeid(8, "0123456789");
}

function randomMissionTitle() {
  return (
    "Mission " +
    choice([
      "de bénévolat",
      "de solidarité",
      "de service",
      "de développement",
      "de formation",
      "de recherche",
      "de conservation",
    ])
  );
}

function randomMissionDescription() {
  return choice([
    "Aide aux personnes âgées",
    "Aide aux personnes en situation de handicap",
    "Aide aux personnes en situation de pauvreté",
    "Aide aux personnes en situation de vulnérabilité",
    "Aide aux personnes en situation de précarité",
    "Aide aux personnes en situation de fragilité",
    "Aide aux personnes en situation de précarité",
    "Aide aux personnes en situation de fragilité",
  ]);
}

function randomPartnerName() {
  const org = choice([
    "FAGE",
    "AAAF",
    "Fondation",
    "Association",
    "Entreprise",
    "Collectif",
    "Groupe",
  ]);
  const suffix = choice([
    randomCity(),
    randomNom(),
    randomNom() + " & " + randomNom(),
  ]);
  return org + " " + suffix;
}

function randomDonorName() {
  return randomPrenom() + " " + randomNom();
}

function randomSubsidyTitle() {
  return (
    "Subvention " +
    choice(["Projet", "Action", "Événement"]) +
    " " +
    randomCity()
  );
}

function randomReference() {
  return "REF-" + makeid(8, "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
}
