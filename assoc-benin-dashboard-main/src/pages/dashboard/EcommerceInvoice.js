/* eslint-disable jsx-a11y/label-has-associated-control */
/* eslint-disable array-callback-return */
// @mui
import { styled } from '@mui/material/styles';
import { Box, Grid, Card, Container, Typography, Stack, TextField, Button } from '@mui/material';
// import { RHFTextField } from '../../components/hook-form';

// routes
import Radio from '@mui/material/Radio';
import RadioGroup from '@mui/material/RadioGroup';
import FormControlLabel from '@mui/material/FormControlLabel';
import FormControl from '@mui/material/FormControl';
import FormLabel from '@mui/material/FormLabel';
import { useEffect, useState } from 'react';
import { LoadingButton } from '@mui/lab';
import { useParams } from 'react-router';
import InputAdornment from '@mui/material/InputAdornment';
import UploadFileIcon from '@mui/icons-material/UploadFile';
import { apiBaseURL, getRequest, postRequest } from '../../utils/api';
import { PATH_DASHBOARD } from '../../routes/paths';
// hooks
import useSettings from '../../hooks/useSettings';
// components
import Page from '../../components/Page';
import Label from '../../components/Label';
import HeaderBreadcrumbs from '../../components/HeaderBreadcrumbs';
// sections
import { InvoiceToolbar } from '../../sections/@dashboard/e-commerce/invoice';
import '../../assets/css/assocInfo.css';

// ----------------------------------------------------------------------

export default function EcommerceInvoice() {
  const { assocCode } = useParams();
  const { themeStretch } = useSettings();

  const [assocData, setAssocData] = useState();
  const [recepisseFile, setRecepisseFile] = useState();
  const [adminObservations, setAdminObservations] = useState();
  const [onTreatedDemandsPage, setOnTreatedDemandsPage] = useState();

  const [choiceValue, setChoiceValue] = useState({
    electedMemberAtAG: 'non',
    membersComposition: 'non',
    membersPresence: 'non',
    criminalRecordValidity: 'non',
  });

  const date = new Date()
  const [referenceAndDate, setReferenceAndDate] = useState({
    referenceMISP: null,
    referenceDAIC: null,
    arriveeMISP: date.toLocaleString(),
    arriveeDAIC: date.toLocaleString(),
  });

  const handleElectedMembersAG = (event) => {
    setChoiceValue((oldState) => ({
      ...oldState,
      electedMemberAtAG: event.target.value,
    }));
  };

  const handleMembersComposition = (event) => {
    setChoiceValue((oldState) => ({
      ...oldState,
      membersComposition: event.target.value,
    }));
  };

  const handleMembersPresence = (event) => {
    setChoiceValue((oldState) => ({
      ...oldState,
      membersPresence: event.target.value,
    }));
  };

  const handleCriminalRecordValidity = (event) => {
    setChoiceValue((oldState) => ({
      ...oldState,
      criminalRecordValidity: event.target.value,
    }));
  };

  const handleReferenceMISP = (event) => {
    setReferenceAndDate((oldState) => ({
      ...oldState,
      referenceMISP: event.target.value,
    }));
  };

  const handleReferenceDAIC = (event) => {
    setReferenceAndDate((oldState) => ({
      ...oldState,
      referenceDAIC: event.target.value,
    }));
  };

  const handleArriveeMISP = (event) => {
    setReferenceAndDate((oldState) => ({
      ...oldState,
      arriveeMISP: event.target.value,
    }));
  };

  const handleArriveeDAIC = (event) => {
    setReferenceAndDate((oldState) => ({
      ...oldState,
      arriveeDAIC: event.target.value,
    }));
  };

  const getRecepisseFile = (event) => {
    setRecepisseFile(event.target.files[0]);
  };

  const getObservations = (event) => {
    setAdminObservations(event.target.value);
  };

  const approveOrRejectRequest = async (decision) => {
    const rejectObject = {
      reference_to_misp: referenceAndDate.referenceMISP,
      reference_to_daic: referenceAndDate.referenceDAIC,
      arrival_to_misp: referenceAndDate.arriveeMISP,
      arrival_to_daic: referenceAndDate.arriveeDAIC,
      observations: adminObservations,
      model_respect: choiceValue.membersComposition,
      presence_list_insertion: choiceValue.membersPresence,
      criminal_records_validity: choiceValue.criminalRecordValidity,
      presidium_members_verification: choiceValue.electedMemberAtAG,
      request_code: assocData.code_requete,
      admin_registration_number: assocData.matricule_admin,
      request_status: 'rejetee',
    };

    const approveObject = {
      reference_to_misp: referenceAndDate.referenceMISP,
      reference_to_daic: referenceAndDate.referenceDAIC,
      arrival_to_misp: referenceAndDate.arriveeMISP,
      arrival_to_daic: referenceAndDate.arriveeDAIC,
      observations: adminObservations,
      model_respect: choiceValue.membersComposition,
      presence_list_insertion: choiceValue.membersPresence,
      criminal_records_validity: choiceValue.criminalRecordValidity,
      presidium_members_verification: choiceValue.electedMemberAtAG,
      request_code: assocData.record.code_requete,
      admin_registration_number: assocData.record.matricule_admin,
      request_status: 'approuvee',
      admin_receipt: recepisseFile,
    };

    try {
      const response = await postRequest(
        '/api/admin_observation',
        decision === 'approve' ? approveObject : rejectObject
      );
      // console.log(response);
      window.location.href = "/dashboard/demandes/traitees"
    } catch (error) {
      console.log(error);
    }
  };

  const formatDate = (dateFromResponse) => dateFromResponse.split(' ')[0];

  const extractFileName = (attachment) => attachment.split('/')[1];

  useEffect(() => {
    const pathName = window.location.pathname;
    setOnTreatedDemandsPage(pathName.includes('/demandes/traitees'));
    getRequest(`/api/get_record/${assocCode}`)
      .then((response) => {
        if (response.data) {
          console.log(response.data);
          setAssocData(response.data);
        }
      })
      .catch((error) => console.log(error));
  }, []);

  return (
    <Page title="Informations sur l'association">
      <Container maxWidth={themeStretch ? false : 'lg'}>
        <HeaderBreadcrumbs
          heading="Informations sur l'association"
          links={[
            { name: 'Dashboard', href: PATH_DASHBOARD.root },
            {
              name: 'Demandes',
            },
            { name: 'Détails' },
          ]}
        />
        {assocData ? (
          <>
            <Card sx={{ pt: 5, px: 5 }}>
              <Grid container>
                <Grid item xs={12} sm={6} sx={{ mb: 5 }}>
                  <Typography variant="h6">{assocData.record.denomination}</Typography>
                </Grid>

                <Grid item xs={12} sm={6} sx={{ mb: 5 }}>
                  <Box sx={{ textAlign: { sm: 'right' } }}>
                    <Label color="success" sx={{ textTransform: 'uppercase', mb: 1 }}>
                      Code d'identification
                    </Label>
                    <Typography variant="h6">{assocData.record.code_requete}</Typography>
                  </Box>
                </Grid>
              </Grid>
              <Grid container alignItems="baseline">
                <Grid item xs={12} sm={6} sx={{ mb: 5 }} container>
                  <Grid item xs={12} sm={6} sx={{ mb: 5 }}>
                    <Typography paragraph variant="overline" sx={{ color: 'text.disabled' }}>
                      Adresse Email
                    </Typography>
                    <Typography variant="body2">{assocData.record.email}</Typography>
                  </Grid>

                  <Grid item xs={12} sm={6} sx={{ mb: 5 }}>
                    <Typography paragraph variant="overline" sx={{ color: 'text.disabled' }}>
                      Numéro de téléphone
                    </Typography>
                    <Typography variant="body2">{assocData.record.telephone}</Typography>
                  </Grid>

                  <Grid item xs={12} sm={6} sx={{ mb: 5 }}>
                    <Typography paragraph variant="overline" sx={{ color: 'text.disabled' }}>
                      Boîte Postale
                    </Typography>
                    <Typography variant="body2">{assocData.record.boite_postale}</Typography>
                  </Grid>

                  <Grid item xs={12} sm={6} sx={{ mb: 5 }}>
                    <Typography paragraph variant="overline" sx={{ color: 'text.disabled' }}>
                      Maison/Immeuble
                    </Typography>
                    <Typography variant="body2">{assocData.record.immeuble}</Typography>
                  </Grid>

                  <Grid item xs={12} sm={6} sx={{ mb: 5 }}>
                    <Typography paragraph variant="overline" sx={{ color: 'text.disabled' }}>
                      Lot/Carré
                    </Typography>
                    <Typography variant="body2">{assocData.record.numero_lot}</Typography>
                  </Grid>

                  <Grid item xs={12} sm={6} sx={{ mb: 5 }}>
                    <Typography paragraph variant="overline" sx={{ color: 'text.disabled' }}>
                      Quartier
                    </Typography>
                    <Typography variant="body2">{assocData.record.quartier}</Typography>
                  </Grid>

                  <Grid item xs={12} sm={6} sx={{ mb: 5 }}>
                    <Typography paragraph variant="overline" sx={{ color: 'text.disabled' }}>
                      Département
                    </Typography>
                    <Typography variant="body2">{assocData.record.departement}</Typography>
                  </Grid>

                  <Grid item xs={12} sm={6} sx={{ mb: 5 }}>
                    <Typography paragraph variant="overline" sx={{ color: 'text.disabled' }}>
                      Commune
                    </Typography>
                    <Typography variant="body2">{assocData.record.commune}</Typography>
                  </Grid>

                  <Grid item xs={12} sm={6} sx={{ mb: 5 }}>
                    <Typography paragraph variant="overline" sx={{ color: 'text.disabled' }}>
                      Arrondissement
                    </Typography>
                    <Typography variant="body2">{assocData.record.arrondissement}</Typography>
                  </Grid>

                  <Grid item xs={12} sm={6} sx={{ mb: 5 }}>
                    <Typography paragraph variant="overline" sx={{ color: 'text.disabled' }}>
                      Nom et prénoms du 1er Responsable
                    </Typography>
                    <Typography variant="body2">{assocData.record.premier_responsable}</Typography>
                  </Grid>

                  <Grid item xs={12} sm={6} sx={{ mb: 5 }}>
                    <Typography paragraph variant="overline" sx={{ color: 'text.disabled' }}>
                      Adresse mail du 1er Responsable
                    </Typography>
                    <Typography variant="body2">{assocData.record.email_premier_responsable}</Typography>
                  </Grid>

                  <Grid item xs={12} sm={12} sx={{ mb: 5 }}>
                    <Typography paragraph variant="overline" sx={{ color: 'text.disabled' }}>
                      Objectifs de l'association
                    </Typography>
                    <Typography variant="body2">{assocData.record.objectifs}</Typography>
                  </Grid>
                </Grid>
                <Grid item xs={12} sm={6} sx={{ mb: 5 }}>
                  <FormControl sx={{ mb: 2 }}>
                    <FormLabel id="members-appartenance">Membres du présidium élus à l'AG ?</FormLabel>
                    <RadioGroup
                      aria-labelledby="members-appartenance"
                      name="members-appartenance"
                      value={assocData.record.verification_membres_presidium ? assocData.record.verification_membres_presidium : choiceValue.electedMemberAtAG}
                      onChange={(e) => handleElectedMembersAG(e)}
                    >
                      <FormControlLabel value="oui" control={<Radio />} label="Oui" />
                      <FormControlLabel value="non" control={<Radio />} label="Non" />
                    </RadioGroup>
                  </FormControl>

                  <FormControl sx={{ mb: 2 }}>
                    <FormLabel id="members-composition">Composition des membres selon le modèle défini ?</FormLabel>
                    <RadioGroup
                      aria-labelledby="members-composition"
                      name="members-composition"
                      value={assocData.record.respect_du_modele ? assocData.record.respect_du_modele : choiceValue.membersComposition}
                      onChange={handleMembersComposition}
                    >
                      <FormControlLabel value="oui" control={<Radio />} label="Oui" />
                      <FormControlLabel value="non" control={<Radio />} label="Non" />
                    </RadioGroup>
                  </FormControl>

                  <FormControl sx={{ mb: 2 }}>
                    <FormLabel id="elected-members-attendance">Liste de présence de tous les membres élus ?</FormLabel>
                    <RadioGroup
                      aria-labelledby="elected-members-attendance"
                      name="elected-members-attendance"
                      value={
                        assocData.record.insertion_liste_de_presence
                          ? assocData.record.insertion_liste_de_presence
                          : choiceValue.membersPresence
                      }
                      onChange={handleMembersPresence}
                    >
                      <FormControlLabel value="oui" control={<Radio />} label="Oui" />
                      <FormControlLabel value="non" control={<Radio />} label="Non" />
                    </RadioGroup>
                  </FormControl>

                  <FormControl sx={{ mb: 2 }}>
                    <FormLabel id="members-criminal-record-validity">
                      Validité du casier judiciare des membres du bureau ?
                    </FormLabel>
                    <RadioGroup
                      aria-labelledby="members-criminal-record-validity"
                      name="members-criminal-record-validity"
                      value={
                        assocData.record.validite_casiers_judiciaires
                          ? assocData.record.validite_casiers_judiciaires
                          : choiceValue.criminalRecordValidity
                      }
                      onChange={handleCriminalRecordValidity}
                    >
                      <FormControlLabel value="oui" control={<Radio />} label="Oui" />
                      <FormControlLabel value="non" control={<Radio />} label="Non" />
                    </RadioGroup>
                  </FormControl>

                  <Typography paragraph variant="overline" sx={{ color: 'text.disabled' }}>
                    Référence et Date d’arrivée du Dossier au MISP
                  </Typography>
                  <Grid container gap={2} sx={{ mb: 5 }}>
                    <TextField
                      label="Référence"
                      value={assocData.record.reference_misp ? assocData.record.reference_misp : referenceAndDate.referenceMISP}
                      onChange={handleReferenceMISP}
                    />
                    <TextField
                      id="date"
                      label="Date"
                      type="date"
                      InputLabelProps={{
                        shrink: true,
                      }}
                      value={formatDate(assocData.record.arrivee_misp ? assocData.record.arrivee_misp : referenceAndDate.arriveeMISP)}
                      onChange={handleArriveeMISP}
                    />
                  </Grid>

                  <Typography paragraph variant="overline" sx={{ color: 'text.disabled' }}>
                    Référence et Date d’arrivée du Dossier à la DAIC
                  </Typography>
                  <Grid container gap={2} sx={{ mb: 5 }}>
                    <TextField
                      label="Référence"
                      value={assocData.record.reference_daic ? assocData.record.reference_daic : referenceAndDate.referenceDAIC}
                      onChange={handleReferenceDAIC}
                    />
                    <TextField
                      id="date"
                      label="Date"
                      type="date"
                      InputLabelProps={{
                        shrink: true,
                      }}
                      value={formatDate(assocData.record.arrivee_daic ? assocData.record.arrivee_daic : referenceAndDate.arriveeDAIC)}
                      onChange={handleArriveeDAIC}
                    />
                  </Grid>

                  <Typography paragraph variant="overline" sx={{ color: 'text.disabled' }}>
                    Date de la tenue de l’AG constitutive
                  </Typography>
                  <Grid container gap={2} sx={{ mb: 5 }}>
                    <TextField
                      id="date"
                      label="Date"
                      type="date"
                      fullWidth
                      InputLabelProps={{
                        shrink: true,
                      }}
                      defaultValue={assocData.record.date_ag ? assocData.record.date_ag : ''}
                    />
                  </Grid>
                </Grid>
                <TextField
                  label="Obervations"
                  multiline
                  rows={7}
                  defaultValue={assocData.record.observations ? assocData.record.observations : ''}
                  fullWidth
                  onChange={(e) => getObservations(e)}
                />
              </Grid>

              <Grid container sx={{ mt: 3 }}>
                <Grid item xs={12} md={12} sx={{ pb: 3 }}>
                  <Typography variant="h6" sx={{ py: 3 }}>
                    Pièces jointes
                  </Typography>
                  <div className="docs-container">
                    {assocData.global_attachments ? (
                      <>
                        {assocData.global_attachments.demande_enregistrement ? (
                          <a
                            target="_blank"
                            key={assocData.global_attachments.code_requete}
                            href={`${apiBaseURL}/storage/${assocData.global_attachments.demande_enregistrement}`}
                            className="doc-file"
                            rel="noreferrer"
                          >
                            {extractFileName(assocData.global_attachments.demande_enregistrement)}
                          </a>
                        ) : null}

                        {assocData.global_attachments.proces_verbal ? (
                          <a
                            target="_blank"
                            key={assocData.global_attachments.code_requete}
                            href={`${apiBaseURL}/storage/${assocData.global_attachments.proces_verbal}`}
                            className="doc-file"
                            rel="noreferrer"
                          >
                            {extractFileName(assocData.global_attachments.proces_verbal)}
                          </a>
                        ) : null}

                        {assocData.global_attachments.membres_ag ? (
                          <a
                            target="_blank"
                            key={assocData.global_attachments.code_requete}
                            href={`${apiBaseURL}/storage/${assocData.global_attachments.membres_ag}`}
                            className="doc-file"
                            rel="noreferrer"
                          >
                            {extractFileName(assocData.global_attachments.membres_ag)}
                          </a>
                        ) : null}

                        {assocData.global_attachments.reglement_interieur ? (
                          <a
                            target="_blank"
                            key={assocData.global_attachments.code_requete}
                            href={`${apiBaseURL}/storage/${assocData.global_attachments.reglement_interieur}`}
                            className="doc-file"
                            rel="noreferrer"
                          >
                            {extractFileName(assocData.global_attachments.reglement_interieur)}
                          </a>
                        ) : null}

                        {assocData.global_attachments.recepisse_de_versement ? (
                          <a
                            target="_blank"
                            key={assocData.global_attachments.code_requete}
                            href={`${apiBaseURL}/storage/${assocData.global_attachments.recepisse_de_versement}`}
                            className="doc-file"
                            rel="noreferrer"
                          >
                            {extractFileName(assocData.global_attachments.recepisse_de_versement)}
                          </a>
                        ) : null}

                        {assocData.global_attachments.statuts ? (
                          <a
                            target="_blank"
                            key={assocData.global_attachments.code_requete}
                            href={`${apiBaseURL}/storage/${assocData.global_attachments.statuts}`}
                            className="doc-file"
                            rel="noreferrer"
                          >
                            {extractFileName(assocData.global_attachments.statuts)}
                          </a>
                        ) : null}

                        {assocData.global_attachments.parti_politique_pieces
                          ? assocData.global_attachments.parti_politique_pieces.map((item, index) => {
                              <a
                                target="_blank"
                                key={index}
                                href={`${apiBaseURL}/storage/${item}`}
                                className="doc-file"
                                rel="noreferrer"
                              >
                                {extractFileName(item)}
                              </a>;
                            })
                          : null}

                        {assocData.global_attachments.presse_pieces
                          ? assocData.global_attachments.presse_pieces.map((item, index) => {
                              <a
                                target="_blank"
                                key={index}
                                href={`${apiBaseURL}/storage/${item}`}
                                className="doc-file"
                                rel="noreferrer"
                              >
                                {extractFileName(item)}
                              </a>;
                            })
                          : null}

                        {assocData.global_attachments.international_pieces
                          ? assocData.global_attachments.international_pieces.map((item, index) => {
                              <a
                                target="_blank"
                                key={index}
                                href={`${apiBaseURL}/storage/${item}`}
                                className="doc-file"
                                rel="noreferrer"
                              >
                                {extractFileName(item)}
                              </a>;
                            })
                          : null}
                      </>
                    ) : null}

                    {!onTreatedDemandsPage ? (
                      <Stack direction="row">
                        <input
                          type="file"
                          name="recepisse"
                          id="recepisse"
                          hidden
                          onChange={(e) => getRecepisseFile(e)}
                          accept=".pdf"
                        />
                        <TextField
                          label="Soumettez le fichier récépissé"
                          id="outlined-start-adornment"
                          sx={{ my: 3, width: '100%' }}
                          value={recepisseFile ? recepisseFile.name : null}
                          InputProps={{
                            startAdornment: (
                              <InputAdornment position="start">
                                {' '}
                                <label htmlFor="recepisse">
                                  {' '}
                                  <UploadFileIcon />{' '}
                                </label>{' '}
                              </InputAdornment>
                            ),
                          }}
                        />
                      </Stack>
                    ) : null}
                  </div>
                </Grid>
              </Grid>
            </Card>
            {!onTreatedDemandsPage ? (
              <Stack direction="row" spacing={1.5} sx={{ mt: 3 }}>
                <Button
                  fullWidth
                  color="error"
                  variant="outlined"
                  size="large"
                  onClick={() => approveOrRejectRequest('reject')}
                >
                  Rejeter la demande
                </Button>
                <LoadingButton
                  fullWidth
                  type="submit"
                  variant="outlined"
                  size="large"
                  onClick={() => approveOrRejectRequest('approve')}
                >
                  Approuver la demande
                </LoadingButton>
              </Stack>
            ) : null}
          </>
        ) : null}
      </Container>
    </Page>
  );
}
