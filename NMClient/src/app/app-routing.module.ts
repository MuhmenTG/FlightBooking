import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SearchFlightsComponent } from './search-flights/search-flights.component';
import { ShowFlightoffersComponent } from './show-flightoffers/show-flightoffers.component';

const routes: Routes = [
  { path: '', component: SearchFlightsComponent },
  { path: 'flight/', component: ShowFlightoffersComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
