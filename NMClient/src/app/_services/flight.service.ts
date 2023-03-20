import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment.development';
import { Flight } from '../_models/flight';

@Injectable({
  providedIn: 'root'
})
export class FlightService {

  private apiUrl = environment.apiUrl + "/flight";
  private amadeusUrl = environment.amadeusApiUrl;

  private httpOptions = {
    headers: new HttpHeaders({ 'Content-Type': 'application/json' }),
    params: new HttpParams()
      .set('grant_type', 'client_credentials')
      .set('client_id', 'xlUodVi30L0U8snyBsa1qenY4BNyUjMA')
      .set('client_secret', 'A2GpGXyewfl0G3gu')
  }

  constructor(private http: HttpClient) { }

  getAccessToken(): Observable<Flight[]> {
    return this.http.get<Flight[]>(this.amadeusUrl, this.httpOptions);
  }

  getFlights(): Observable<Flight[]> {
    return this.http.get<Flight[]>(this.apiUrl + "/" + "searchFlights");
  }
}
