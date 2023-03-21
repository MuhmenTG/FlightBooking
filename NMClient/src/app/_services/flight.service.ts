import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment.development';
import { AccessTokenResponse } from '../_models/accessTokenResponse';
import { Flight } from '../_models/flight';
import { SearchFlight } from '../_models/searchFlight';

@Injectable({
  providedIn: 'root'
})
export class FlightService {

  private apiUrl = environment.apiUrl + "/flight";
  private amadeusUrl = environment.amadeusApiUrl;

  private accessTokenParameters = new HttpParams()
      .set('grant_type', 'client_credentials')
      .set('client_id', '73Vz7GstLSf9xaHWx0fPMH6PRg6wYqjT')
      .set('client_secret', 'lSJEDh6AwHwH5omy')


  private token: AccessTokenResponse = {access_token: ''};
  private body: SearchFlight = {originLocationCode: "", destinationLocationCode: "", departureDate: "", returnDate:"", adults: 1};

  private httpOptions = {
    headers: new HttpHeaders({
      'Content-Type': 'application/x-www-form-urlencoded',
      'Authorization': 'Bearer ' + this.token.access_token
    })
  };
  
  constructor(private http: HttpClient) { }

  getAccessToken(): void {
    this.http.post<AccessTokenResponse>(this.amadeusUrl, this.accessTokenParameters).subscribe(x => {this.token.access_token = x.access_token; console.log(this.token)});
  }

  getFlights(): Observable<Flight[]> {
    return this.http.post<Flight[]>(this.apiUrl + "/searchFlights", this.body, this.httpOptions);
  }
}
