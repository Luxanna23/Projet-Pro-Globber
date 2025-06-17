describe('Réservation d’une annonce', () => {
  it('affiche les éléments de réservation', () => {
    cy.visit('/annonces/1') // met une vraie annonce avec ID existant
    cy.get('button').contains('Réserver').should('be.visible')
  })
})

//npx cypress open pour les lancer