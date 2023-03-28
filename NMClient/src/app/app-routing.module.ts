import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SearchFlightsComponent } from './search-flights/search-flights.component';
import { ShowFlightofferComponent } from './show-flightoffer/show-flightoffer.component';

const routes: Routes = [
  { path: '', component: SearchFlightsComponent },
  { path: '/flight', component: ShowFlightofferComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
