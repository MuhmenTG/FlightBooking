import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment.development';
import { AccessTokenResponse } from '../_models/accessTokenResponse';
import { Flight } from '../_models/flight';

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
  
  constructor(private http: HttpClient) { }

  getAccessToken(): Observable<AccessTokenResponse> {
    return this.http.post<AccessTokenResponse>(this.amadeusUrl, this.accessTokenParameters);
  }

  getFlights(): Observable<Flight[]> {
    return this.http.get<Flight[]>(this.apiUrl + "/" + "searchFlights");
  }
}
