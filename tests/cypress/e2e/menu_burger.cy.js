describe('Menu Burger', () => {
  it('affiche le menu quand on clique sur le bouton burger', () => {
    cy.visit('/')
    cy.get('#burger').click()
    cy.get('#menu').should('be.visible')
  })
})