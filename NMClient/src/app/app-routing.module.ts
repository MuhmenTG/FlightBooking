import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SearchFlightsComponent } from './search-flights/search-flights.component';
import { SearchHotelsComponent } from './search-hotels/search-hotels.component';
import { LoginComponent } from './login/login.component';
import { AdminComponent } from './admin/admin.component';

const routes: Routes = [
  { path: '', component: SearchFlightsComponent },
  { path: 'hotels', component: SearchHotelsComponent },
  { path: 'login', component: LoginComponent },
  { path: 'admin', component: AdminComponent},
  { path: 'showbooking', component: AdminComponent}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
