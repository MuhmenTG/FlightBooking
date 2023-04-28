import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SearchFlightsComponent } from './search-flights/search-flights.component';
import { SearchHotelsComponent } from './search-hotels/search-hotels.component';

const routes: Routes = [
  { path: '', component: SearchFlightsComponent },
  { path: 'hotels', component: SearchHotelsComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
