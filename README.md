# eventos-plugin
O objetivo deste exercício é desenvolveres um plugin WordPress simples, que permita gerir e apresentar eventos. 

O plugin deverá criar um Custom Post Type chamado “Eventos” e, através do plugin Advanced Custom Fields (ACF), cada evento deverá incluir os seguintes campos: data do evento, local e organizador. A imagem de destaque deverá ser gerida através da funcionalidade nativa do WordPress. 

 

Deverás ainda implementar um shortcode chamado [eventos_futuros], responsável por listar os eventos cuja data seja igual ou superior à atual. Esse shortcode deverá aceitar um parâmetro opcional limite, permitindo restringir o número de eventos apresentados. A listagem deverá ser apresentada sob a forma de grelha de três colunas. Para a camada visual, pedimos que utilizes uma framework CSS como Bootstrap ou TailwindCSS. Não procuramos um design complexo, mas esperamos cuidado com alinhamentos, espaçamentos e legibilidade. 

 

O plugin deverá ainda incluir um template single para os eventos, onde serão apresentados o título, a data, o local, o organizador, a imagem de destaque e o conteúdo do evento. 
