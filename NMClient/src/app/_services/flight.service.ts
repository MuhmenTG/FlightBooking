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
    headers: new HttpHeaders({ 'Content-Type': 'application/x-www-form-urlencoded' })
  }

  private body = "grant_type=client_credentials&client_id=73Vz7GstLSf9xaHWx0fPMH6PRg6wYqjT&client_secret=lSJEDh6AwHwH5omy"

  constructor(private http: HttpClient) { }

  getAccessToken(): Observable<{}> {
    return this.http.post<{}>(this.amadeusUrl, 
    this.body, 
    this.httpOptions);
  }

  getFlights(): Observable<Flight[]> {
    return this.http.get<Flight[]>(this.apiUrl + "/" + "searchFlights");
  }
}
